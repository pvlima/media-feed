# pvlima/media-feed

API para obter o feed de publicações de uma página do Instagram

## Instalação

   É recomendável instalar o pacote usando o composer. Basta digitar o seguinte comando no terminal:
      
      composer require pvlima/media-feed


## Exemplo:

   O construtor da classe Pvlima\MediaFeed\Instagram\InstagramAPI() deve receber a instância de Pvlima\MediaFeed\Cache\CacheManager('diretorio/de/cache') para que guarde informações importantes da requisição.

    include 'vendor/autoload.php';

    $cache = new \Pvlima\MediaFeed\Instagram\Cache\CacheManager(__DIR__ . '/cache/');
    
    $api   = new \Pvlima\MediaFeed\Instagram\InstagramAPI($cache);
    
    $api->setUserName('pvlima2');

    
    $feed = $api->getFeed();
    echo print_r($feed);
    
   ### Outras Configurações

   Em cada request são exibidas as últimas 12 postagens da página, caso necessite paginar e ir visualizando as postagens seguintes, deve ser utilizado o cursor de paginação. Em cada request, como no exemplo acima, é informado o endCursor, que é o cursor da próxima página de posts. Para visitar a próxima página, basta informar o endCursor obtido no request anterior:
   
    include 'vendor/autoload.php';

    $cache = new \Pvlima\MediaFeed\Instagram\Cache\CacheManager(__DIR__ . '/cache/');
    
    $api   = new \Pvlima\MediaFeed\Instagram\InstagramAPI($cache);
    
    $api->setUserName('pvlima2');
    
    $api->setEndCursor('AQB3YFhMu38VUyjhyvLe3EkoV0zvW5In_cDK8ZD8h7VbJOhKp5CRCq5lsXJJ2fjsubA');
    
    $feed = $api->getFeed();
    echo print_r($feed);

   Neste caso, a variável $feed é uma instância de \Pvlima\MediaFeed\Instagram\Model\Feed, e pode ser manipulada de acordo com os métodos correspondentes:

    echo $feed->getFullName();
    echo $feed->getBiography();
    echo $feed->getFollowers();

   É possível também trabalhar diretamente com as postagens através do método $feed->getMedias() que retorna um array contendo as postagens que são instâncias de \Pvlima\MediaFeed\Instagram\Model\Media, e podem ser manipuladas de acordo com os métodos correspondentes:

    foreach($feed->getMedias() as $media){
          //Para obter o link da imagem do post
          echo $media->getThumbnailSrc();
    }
