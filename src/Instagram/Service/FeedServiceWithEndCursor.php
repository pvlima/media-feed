<?php

namespace Pvlima\MediaFeed\Instagram\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException;
use Pvlima\MediaFeed\Instagram\Cache\Cache;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;

class FeedServiceWithEndCursor extends FeedServiceAbstract
{
    /**
     * @var string
     */
    private $endCursor;

    const REQUIRES_COOKIES_KEYS = ['rur', 'mid', 'mcd', 'urlgen', 'csrftoken'];

    /**
     * JsonTransportFeed constructor.
     *
     * @param Client            $client
     * @param                   $endCursor
     * @param CacheManager|null $cacheManager
     */
    public function __construct(Client $client, $endCursor, CacheManager $cacheManager = null)
    {
        $this->endCursor = $endCursor;
        parent::__construct($cacheManager, $client);
    }

    /**
     * @param $variables
     *
     * @return string
     */
    private function generateGis($variables)
    {
        return md5(json_encode($variables));
    }

    /**
     * @param string $userName
     * @param int    $limit
     *
     * @return mixed
     *
     * @throws InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Instagram\Exception\InstagramCacheException
     */
    public function fetchData(string $userName, int $limit)
    {
        /** @var Cache $cache */
        $cache = $this->cacheManager->getCache($userName);

        $variables = [
            'id'    => $cache->getUserId(),
            'first' => $limit,
            'after' => $this->endCursor,
        ];

        $cookieJar = CookieJar::fromArray($cache->getCookie(), 'www.instagram.com');

        $headers = [
            'headers' => [
                'user-agent'       => self::USER_AGENT,
                'x-requested-with' => 'XMLHttpRequest',
                'x-instagram-gis'  => $this->generateGis($variables),
                'x-csrftoken'      => $cache->getCsrfToken(),
            ],
            'cookies' => $cookieJar
        ];

        $endpoint = self::INSTAGRAM_ENDPOINT . 'graphql/query/?query_hash=' . self::QUERY_HASH . '&variables=' . json_encode($variables);

        $res = $this->client->request('GET', $endpoint, $headers);

        $data = (string)$res->getBody();
        $data = json_decode($data);

        if ($data === null) {
            throw new InstagramAPIException(json_last_error_msg());
        }

        // save to cache for next request
        $newCache = new Cache();
        $newCache->setUserId($cache->getUserId());
        if ($res->hasHeader('Set-Cookie')) {
            $saveCookies = [];
            foreach ($res->getHeaders()['Set-Cookie'] as $cookie) {
                $setCookie = SetCookie::fromString($cookie);
                if (in_array($setCookie->getName(), self::REQUIRES_COOKIES_KEYS, true)) {
                    $saveCookies[] = $cookie;
                }
            }
            $newCache->setCookie($saveCookies);
        }

        $this->cacheManager->set($newCache, $userName);

        return $data->data->user;
    }
}
