<?php

namespace Pvlima\MediaFeed\Instagram\Cache;

use GuzzleHttp\Cookie\CookieJar;
use Pvlima\MediaFeed\Instagram\Exception\CacheException;

class CacheManager
{
    /**
     * @var string
     */
    private $cacheDir = null;

    /**
     * CacheManager constructor.
     *
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param $userId
     *
     * @return string
     */
    private function getCacheFile($userId)
    {
        return $this->cacheDir . $userId . '.cache';
    }

    /**
     * @var bool
     */
    public $sessionName = false;

    /**
     * @param $user
     *
     * @return string
     */
    private function getSessionFile($user)
    {
        return $this->cacheDir . $user . '.session';
    }

    /**
     * @param $userId
     *
     * @return Cache|mixed
     */
    public function getCache($userId)
    {
        if (is_file($this->getCacheFile($userId))) {
            $handle = fopen($this->getCacheFile($userId), 'r');
            $data   = fread($handle, filesize($this->getCacheFile($userId)));
            $cache  = unserialize($data);

            fclose($handle);

            if ($cache instanceof Cache) {
                return $cache;
            }
        }

        return new Cache();
    }

    /**
     * @param Cache $cache
     * @param $userName
     *
     * @throws CacheException
     */
    public function set(Cache $cache, $userName)
    {
        if (!is_writable(dirname($this->getCacheFile($userName)))) {
            throw new CacheException('Cache folder is not writable');
        }

        $data   = serialize($cache);
        $handle = fopen($this->getCacheFile($userName), 'w+');

        fwrite($handle, $data);
        fclose($handle);
    }

    /**
     * @param           $userName
     * @param CookieJar $cookies
     *
     * @throws CacheException
     */
    public function setSession($userName, CookieJar $cookieJar)
    {
        if (!is_writable(dirname($this->getSessionFile($userName)))) {
            throw new CacheException('Cache folder is not writable');
        }

        $data   = serialize($cookieJar);
        $handle = fopen($this->getSessionFile($userName), 'w+');

        fwrite($handle, $data);
        fclose($handle);
    }

    /**
     * @param $userName
     *
     * @return null
     */
    public function getSession($username = null)
    {
        $this->sessionName = $username ? $username : $this->sessionName;

        if (is_file($this->getSessionFile($this->sessionName))) {
            $handle = fopen($this->getSessionFile($this->sessionName), 'r');
            $data   = fread($handle, filesize($this->getSessionFile($this->sessionName)));
            $session  = unserialize($data);

            fclose($handle);

            return $session;
        }

        return null;
    }
}
