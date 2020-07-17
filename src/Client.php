<?php

namespace WhatsappClient;

use Closure;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCapabilities;
use Symfony\Component\Panther\Client as PantherClient;

class Client
{
    /**
     * @var PantherClient
     */
    private $client;
    private $sessionFile;
    private $qrcodeCallback;
    public function __construct($host, ?WebDriverCapabilities $capabilities = null, $sessionFile = '.session')
    {
        if (!$capabilities) {
            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        }
        $this->sessionFile = $sessionFile;
        $this->setClient(PantherClient::createSeleniumClient($host, $capabilities));
    }

    /**
     * @param PantherClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setQrcodeCallback(Closure $closure)
    {
        $this->qrcodeCallback = $closure;
    }

    public function sessionStart()
    {
        if (!$this->loadSessionFronFile()) {
            $this->login();
        }
    }

    public function sendMessage(string $phoneNumber, string $message)
    {
        $message = urlencode($message);
        $this->client->executeScript(<<<SCRIPT
            aHref = document.getElementById('aHref');
            if (aHref == null || typeof(aHref) == 'undefined') {
                aHref = document.createElement('a');
                exist = false;
                aHref.appendChild(document.createTextNode('.'))
            } else {
                exist = true;
            }
            aHref.setAttribute('href', "https://wa.me/$phoneNumber?text=$message");
            aHref.setAttribute('id', "aHref");
            if (!exist) {
                document.getElementsByTagName('span')[0].appendChild(aHref)
            }
            SCRIPT
        );
        $this->client
            ->findElement(WebDriverBy::cssSelector('#aHref'))
            ->click();
        $error = $this->client
            ->findElement(WebDriverBy::cssSelector('[data-animate-modal-body="true"]'))
            ->getText();
        if ($error) {
            throw new \Exception($error, 1);
        }
        $this->client
            ->findElement(WebDriverBy::cssSelector('footer button:not([tabindex])'))
            ->click();
    }

    private function loadSessionFronFile()
    {
        if (!file_exists($this->sessionFile)) {
            return false;
        }
        $json = file_get_contents($this->sessionFile);
        if (!$json) {
            return false;
        }
        $this->client->request('GET', 'https://web.whatsapp.com/');
        $this->client->executeScript(<<<SCRIPT
            localStorage.clear()
            let temp = $json
            Object.keys(temp).map(function(objectKey, index) {
                localStorage.setItem(objectKey, temp[objectKey])
            });
            SCRIPT
        );
        $this->client->request('GET', 'https://web.whatsapp.com/');
        try {
            $menu = $this->client->findElement(WebDriverBy::cssSelector('[data-testid="menu"][data-icon="menu"]'));
        } catch (\Exception $e) { }
        return !empty($menu);
    }

    private function login()
    {
        $this->client->request('GET', 'https://web.whatsapp.com/');
        $refBefore = '';
        do {
            try {
                $element = $this->client->findElement(WebDriverBy::cssSelector('.landing-main [data-ref]'));
                $ref = $element->getAttribute('data-ref');
                if ($refBefore != $ref) {
                    $refBefore = $ref;
                    if ($this->qrcodeCallback) {
                        call_user_func($this->qrcodeCallback, $element->takeElementScreenshot(), $this);
                    }
                }
            } catch (\Exception $e) { }
            try {
                $menu = $this->client->findElement(WebDriverBy::cssSelector('[data-testid="menu"][data-icon="menu"]'));
            } catch (\Exception $e) { }
            sleep(1);
        } while(empty($menu));
    }

    public function __destruct()
    {
        $json = $this->client->executeScript('return localStorage');
        file_put_contents($this->sessionFile, json_encode($json));
        $this->client->quit();
    }
}
