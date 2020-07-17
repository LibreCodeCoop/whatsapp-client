<?php

// Run Selenium in host before run this script if you need see the browser
// Example:
// java -jar selenium-server-standalone-3.141.59.jar

use Facebook\WebDriver\Remote\DesiredCapabilities;
use WhatsappClient\Client;

require 'vendor/autoload.php';

$capabilities = DesiredCapabilities::firefox();
$WhatsappClient = new Client('http://172.17.0.1:4444/wd/hub', $capabilities);
$WhatsappClient->setQrcodeCallback(function ($screenshot, $client) {
    
});
$WhatsappClient->sessionStart();

// Send message
$phoneNumber = '19999999999999';
$text = 'Test message';
$WhatsappClient->sendMessage($phoneNumber, $text);