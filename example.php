<?php

require_once __DIR__ . '/vendor/autoload.php';

$cache = new \Pvlima\MediaFeed\Instagram\Cache\CacheManager(__DIR__ . '/cache/');
$api   = new \Pvlima\MediaFeed\Instagram\InstagramAPI($cache);
$api->login('pvlima2', '********');
$api->setUserName('indoorchannel');
// $api->setEndCursor('QVFBNWVjSkhqZlUwMTZqaDNjbWZHdmdwdjdydTZTUktUZ1pfZ0hKOXVvcGJKUk5WOGFkRFhIWWNJMG85bi15LTBVMmZ3MmlzajFMOGVEM21fcEEyN3NHSg==');

header('Content-Type: application/json');
$feed = $api->getFeed(50);
echo json_encode($feed);