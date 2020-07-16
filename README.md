# WhatsApp web php client

A client for WhatsApp web in PHP

## Run

```bash
docker-compose up
```

Save the follow PHP code content in file `test.php` and run:

```bash
docker-compose exec php7 php test.php
```

```php
<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use WhatsappClient\Client;

require 'vendor/autoload.php';

$WhatsappClient = new Client('http://172.17.0.1:4444/wd/hub');
$WhatsappClient->setQrcodeCallback(function ($screenshot, $client) {
    // image here
});
$WhatsappClient->sessionStart();
```

## Suggestions of new features

* Send qrcode to a Telegram bot
* Send message to person
* Hook to intercept all messages and send to Telegram Bot in conversation with predefined Telegram user
* Respond message in Telegram bot and forward the response to WhatsApp contact
* Define default reply message every time when receive new message in WhatsApp
* Identify when WhatsApp is offline and notify to user in Telegram Group to open the Whatsapp Client in cellphone