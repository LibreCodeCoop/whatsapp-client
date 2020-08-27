<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use WhatsappClient\TelegramClient;

require 'vendor/autoload.php';

if (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__);
    $dotenv->load();
}

$capabilities = DesiredCapabilities::firefox();
$TelegramClient = new TelegramClient('http://selenium-hub:4444/wd/hub', $capabilities);
$TelegramClient->sessionStart();

// Send message
$phoneNumber = '19999999999999';
$text = 'Test message';
try {
    $TelegramClient->sendMessage($phoneNumber, $text);
} catch (\Throwable $th) {
    echo $th->getMessage();
}