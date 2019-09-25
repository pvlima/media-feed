<?php

namespace Pvlima\MediaFeed\Instagram\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Pvlima\MediaFeed\Instagram\Exception\InstagramAuthException;
use Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException;
use Pvlima\MediaFeed\Instagram\Service\FeedServiceAbstract;

class Login
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * @param $username
     * @param $password
     *
     * @return CookieJar
     *
     * @throws InstagramAuthException
     * @throws InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute($username, $password)
    {
        $query = $this->client->request('GET', FeedServiceAbstract::INSTAGRAM_ENDPOINT, [
            'headers' => [
                'user-agent' => FeedServiceAbstract::USER_AGENT
            ]
        ]);

        $html = (string)$query->getBody();

        preg_match('/<script type="text\/javascript">window\._sharedData\s?=(.+);<\/script>/', $html, $matches);

        if (!isset($matches[1])) {
            throw new InstagramAPIException('Unable to extract JSON data');
        }

        $data = json_decode($matches[1]);

        $cookieJar = new CookieJar();

        $query = $this->client->request('POST', FeedServiceAbstract::AUTH_URL, [
            'form_params' => [
                'username' => $username,
                'password' => $password,
            ],
            'headers'     => [
                'cookie'      => 'ig_cb=1; csrftoken=' . $data->config->csrf_token,
                'referer'     => FeedServiceAbstract::INSTAGRAM_ENDPOINT,
                'x-csrftoken' => $data->config->csrf_token,
                'user-agent'  => FeedServiceAbstract::USER_AGENT,
            ],
            'cookies' => $cookieJar
        ]);

        if ($query->getStatusCode() !== 200) {
            throw new InstagramAuthException('Unknown error');
        } else {
            $response = json_decode((string)$query->getBody());
            if ($response->authenticated == true) {
                return $cookieJar;
            } else {
                throw new InstagramAuthException('Wrong login / password');
            }
        }
    }
}