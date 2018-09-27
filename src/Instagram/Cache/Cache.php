<?php

namespace Pvlima\MediaFeed\Instagram\Cache;

class Cache
{
    /**
     * @var string
     */
    private $rhxGis;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $biography;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $profilePic;

    /**
     * @var int
     */
    private $followers;

    /**
     * @var int
     */
    private $following;

    /**
     * @var string
     */
    private $externalUrl;

    /**
     * @var array
     */
    private $cookie = [];

    /**
     * @return string
     */
    public function getRhxGis()
    {
        return $this->rhxGis;
    }

    /**
     * @param string $rhxGis
     */
    public function setRhxGis($rhxGis)
    {
        $this->rhxGis = $rhxGis;
    }

    /**
     * @return array
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @param array $cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePic;
    }

    /**
     * @param string $profilePic
     */
    public function setProfilePicture($profilePic)
    {
        $this->profilePic = $profilePic;
    }

    /**
     * @return int
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param int $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }

    /**
     * @return int
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param int $following
     */
    public function setFollowing($following)
    {
        $this->following = $following;
    }

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * @param string $externalUrl
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

}
