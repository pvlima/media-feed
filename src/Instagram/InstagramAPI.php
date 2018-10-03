<?php

namespace Pvlima\MediaFeed\Instagram;

use GuzzleHttp\Client;
use Pvlima\MediaFeed\Instagram\Builder\JsonBuilder;
use Pvlima\MediaFeed\Instagram\Cache\CacheManager;
use Pvlima\MediaFeed\Instagram\Service\FeedService;

use Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException;

class InstagramAPI
{
    /**
     * Gerenciador de cache para guardar os resultados
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * Cliente HTTP
     * @var Client
     */
    private $client = null;

    /**
     * Username do Instagram que será gerado o Feed
     * @var string
     */
    private $userName;

    /**
     * Hash do cursor dos resultados para paginação
     * @var string
     */
    private $endCursor = null;


    /**
     * @param Client|null $client
     * @param CacheManager|null $cacheManager
     */
    public function __construct(CacheManager $cacheManager, Client $client = null)
    {
        $this->cacheManager = $cacheManager;
        $this->client       = $client ?: new Client();
    }

    /**
     * Função principal que retorna o Feed do perfil do instagram
     * @return \Pvlima\MediaFeed\Instagram\Builder\Result\Feed
     * @throws \Pvlima\MediaFeed\Instagram\Exception\CacheException
     * @throws \Pvlima\MediaFeed\Instagram\Exception\InstagramAPIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFeed()
    {
        if (empty($this->userName)) {
            throw new InstagramAPIException('Username não pode ser vazio!');
        }

        $feed     = new FeedService($this->cacheManager, $this->client, $this->endCursor);
        $dataFetched = $feed->fetchData($this->userName);

        $builder = new ResultBuilder($dataFetched);

        return $builder->getDataBuild();
    }

    /**
     * Informar o username do perfil do instagram
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Caso queria paginar, informar o cursor da próxima página
     * @param string $endCursor
     */
    public function setEndCursor($endCursor)
    {
        $this->endCursor = $endCursor;
    }

}
