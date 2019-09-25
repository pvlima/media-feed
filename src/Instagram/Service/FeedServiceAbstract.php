<?php

namespace Pvlima\MediaFeed\Instagram\Service;

use GuzzleHttp\Client;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;

abstract class FeedServiceAbstract
{
    
    const INSTAGRAM_ENDPOINT = 'https://www.instagram.com/';
    const AUTH_URL           = 'https://www.instagram.com/accounts/login/ajax/';
    const USER_AGENT         = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.75 Safari/537.36';
    const QUERY_HASH         = '58b6785bea111c67129decbe6a448951';

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var Client
     */
    protected $client;

    /**
     * FeedServiceAbstract constructor.
     * @param Client $client
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager, Client $client)
    {
        $this->cacheManager = $cacheManager;
        $this->client       = $client;
    }

}
