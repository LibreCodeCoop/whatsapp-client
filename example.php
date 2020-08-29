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

$TelegramClient->loop(function(TelegramClient $client, $messages) {
    foreach ($messages as $message) {
        $client->telegram()->sendMessage([
            'chat_id' => $_ENV['TELEGRAM_CHAT_ID'],
            'text' => $message['body']
        ]);
    }
});
// $TelegramClient->WsapiWrapper->getProfilePicFromId('199999999999@c.us');
// $TelegramClient->WsapiWrapper->sendMessage('199999999999@c.us', '<3');