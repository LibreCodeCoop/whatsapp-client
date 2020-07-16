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