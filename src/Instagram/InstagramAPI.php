<?php

namespace Pvlima\MediaFeed\Instagram;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Pvlima\MediaFeed\Instagram\Auth\Login;
use Pvlima\MediaFeed\Instagram\Exception\CacheException;
use Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException;
use Pvlima\MediaFeed\Instagram\Model\BuildWithoutEndCursor;
use Pvlima\MediaFeed\Instagram\Model\BuildWithEndCursor;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;
use Pvlima\MediaFeed\Instagram\Service\FeedServiceWithoutEndCursor;
use Pvlima\MediaFeed\Instagram\Service\FeedServiceWithEndCursor;

class InstagramAPI
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $endCursor = null;

    /**
     * Api constructor.
     *
     * @param Client|null       $client
     * @param CacheManager|null $cacheManager
     */
    public function __construct(CacheManager $cacheManager = null, Client $client = null)
    {
        $this->cacheManager = $cacheManager;
        $this->client       = $client ?: new Client();
    }

    /**
     * @param integer $limit
     *
     * @return Hydrator\Component\Feed
     *
     * @throws CacheException
     * @throws InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getFeed($limit = 12)
    {
        if (empty($this->userName)) {
            throw new InstagramAPIException('Username cannot be empty');
        }

        if ($this->endCursor) {
            if (!$this->cacheManager instanceof CacheManager) {
                throw new CacheException('CacheManager object must be specified to use pagination');
            }

            $feed     = new FeedServiceWithEndCursor($this->client, $this->endCursor, $this->cacheManager);
            $build = new BuildWithEndCursor();
        } else {
            $feed     = new FeedServiceWithoutEndCursor($this->client, $this->cacheManager);
            $build = new BuildWithoutEndCursor();
        }

        $dataFetched = $feed->fetchData($this->userName, $limit);

        $build->setData($dataFetched);

        return $build->getHydratedData();
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @param string $endCursor
     */
    public function setEndCursor($endCursor)
    {
        $this->endCursor = $endCursor;
    }

    /**
     * @param             $username
     * @param             $password
     * @param Client|null $client
     *
     * @throws Exception\InstagramAuthException
     * @throws CacheException
     * @throws InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login($username, $password, Client $client = null)
    {
        if (!$this->cacheManager instanceof CacheManager) {
            throw new CacheException('CacheManager is required with login');
        }

        if($cookieJar = $this->cacheManager->getSession($username)) {
            $sessionid = $cookieJar->getCookieByName('sessionid');

            $expires = ($sessionid->getExpires() - time());
            if($expires > 0) {
                return true;
            }
        }

        $login   = new Login($client);
        $cookies = $login->execute($username, $password);

        if ($cookies instanceof CookieJar) {
            $this->cacheManager->sessionName = $username;
            $this->cacheManager->setSession($username, $cookies);
            return true;
        }

        return false;
    }
}
