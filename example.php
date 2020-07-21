<?php

// Run Selenium in host before run this script if you need see the browser
// Example:
// java -jar selenium-server-standalone-3.141.59.jar

use WhatsappClient\TelegramClient;

require 'vendor/autoload.php';

if (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__);
    $dotenv->load();
}

$TelegramClient = new TelegramClient();
$TelegramClient->sessionStart();

// Send message
$phoneNumber = '19999999999999';
$text = 'Test message';
try {
    $TelegramClient->sendMessage($phoneNumber, $text);
} catch (\Throwable $th) {
    echo $th->getMessage();
}