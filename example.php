<?php

require_once __DIR__ . '/vendor/autoload.php';

$cache = new \Pvlima\MediaFeed\Instagram\Cache\CacheManager(__DIR__ . '/cache/');
$api   = new \Pvlima\MediaFeed\Instagram\InstagramAPI($cache);
$api->setUserName('pvlima2');
// $api->setEndCursor('AQB3YFhMu38VUyjhyvLe3EkoV0zvW5In_cDK8ZD8h7VbJOhKp5CRCq5lsXJJ2fjsubA');

header('Content-Type: application/json');
$feed = $api->getFeed();
echo json_encode($feed);