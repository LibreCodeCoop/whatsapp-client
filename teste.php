<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use WhatsappClient\Client;

require 'vendor/autoload.php';

$capabilities = DesiredCapabilities::firefox();
$WhatsappClient = new Client('http://172.17.0.1:4444/wd/hub', $capabilities);
$client = $WhatsappClient->getClient();

$WhatsappClient->login(function(){});

return;

$cookies = (array) $client->manage()->getCookies();
foreach($cookies as $key => $cookie) {
    $cookies[$key] = $cookie->toArray();
}
print_r($cookies);
print_r(serialize($cookies));

return;