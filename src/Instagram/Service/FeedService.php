<?php

namespace Pvlima\MediaFeed\Instagram\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

use Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException;
use Pvlima\MediaFeed\Instagram\Cache\Cache;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;

class FeedService extends FeedServiceAbstract
{
    /**
     * @var string
     */
    private $endCursor;

    /**
     * JsonTransportFeed constructor.
     * @param CacheManager $cacheManager
     * @param Client $client
     * @param $endCursor
     */
    public function __construct(CacheManager $cacheManager, Client $client, $endCursor = null)
    {
        $this->endCursor = $endCursor;
        parent::__construct($cacheManager, $client);
    }

    /**
     * @param $rhxgis
     * @param $variables
     * @return string
     */
    private function generateGis($rhxgis, $variables)
    {
        return md5($rhxgis . ':' . json_encode($variables));
    }

    /**
     * @param $userName
     * @return mixed
     * @throws InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Instagram\Exception\CacheException
     */
    public function fetchData($userName)
    {
        if($this->endCursor !== null){

            /** @var Cache $cache */
            $cache = $this->cacheManager->getCache($userName);

            $variables = [
                'id'    => $cache->getUserId(),
                'first' => '12',
                'after' => $this->endCursor,
            ];

            $cookieJar = CookieJar::fromArray($cache->getCookie(), 'www.instagram.com');

            $headers = [
                'headers' => [
                    'user-agent'       => self::USER_AGENT,
                    'x-requested-with' => 'XMLHttpRequest',
                    'x-instagram-gis'  => $this->generateGis($cache->getRhxGis(), $variables)
                ],
                'cookies' => $cookieJar
            ];

            $endpoint = self::INSTAGRAM_ENDPOINT . 'graphql/query/?query_hash=' . self::QUERY_HASH . '&variables=' . urlencode(json_encode($variables));

            $res = $this->client->request('GET', $endpoint, $headers);

            $data = (string)$res->getBody();
            $data = json_decode($data);

            if ($data === null) {
                throw new InstagramAPIException(json_last_error_msg());
            }

            /*
            if ($res->hasHeader('Set-Cookie')) {
                $cache->setCookie($res->getHeaders()['Set-Cookie']);
            }

            $this->cacheManager->set($cache, $userName);
            */

            $result = $data->data->user;
            $result->id = $cache->getUserId();
            $result->username = $cache->getUserName();
            $result->biography = $cache->getBiography();
            $result->full_name = $cache->getFullName();
            $result->profile_pic_url_hd = $cache->getProfilePicture();
            $result->edge_followed_by = new \stdClass();
            $result->edge_followed_by->count = $cache->getFollowers();
            $result->edge_follow = new \stdClass();
            $result->edge_follow->count = $cache->getFollowing();
            $result->external_url = $cache->getExternalUrl();

            return $result;

        } else {

            $endpoint = self::INSTAGRAM_ENDPOINT . $userName . '/';

            $headers = [
                'headers' => [
                    'user-agent' => self::USER_AGENT
                ]
            ];

            $res = $this->client->request('GET', $endpoint, $headers);

            $html = (string)$res->getBody();

            preg_match('/<script type="text\/javascript">window\._sharedData\s?=(.+);<\/script>/', $html, $matches);

            if (!isset($matches[1])) {
                throw new InstagramAPIException('Unable to extract JSON data');
            }

            $data = json_decode($matches[1]);

            if ($data === null) {
                throw new InstagramAPIException(json_last_error_msg());
            }

            $result = $data->entry_data->ProfilePage[0]->graphql->user;

            $newCache = new Cache();
            $newCache->setRhxGis($data->rhx_gis);
            $newCache->setUserId($result->id);
            $newCache->setUserName($result->username);
            $newCache->setBiography($result->biography);
            $newCache->setFullName($result->full_name);
            $newCache->setProfilePicture($result->profile_pic_url_hd);
            $newCache->setFollowers($result->edge_followed_by->count);
            $newCache->setFollowing($result->edge_follow->count);
            $newCache->setExternalUrl($result->external_url);
            if ($res->hasHeader('Set-Cookie')) {
                $newCache->setCookie($res->getHeaders()['Set-Cookie']);
            }

            $this->cacheManager->set($newCache, $userName);

            return $result;

        }
    }
}
