<?php

namespace Pvlima\MediaFeed\Instagram\Service;

use GuzzleHttp\Client;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;

abstract class FeedServiceAbstract
{
    const INSTAGRAM_ENDPOINT = 'https://www.instagram.com/';
    const USER_AGENT         = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36';
    const QUERY_HASH         = 'c91bcc5598604b1249aaefba78af1ffc';

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
