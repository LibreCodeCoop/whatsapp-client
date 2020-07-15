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
    public function __construct($host, ?WebDriverCapabilities $capabilities = null)
    {
        if (!$capabilities) {
            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        }
        $this->setClient(PantherClient::createSeleniumClient($host, $capabilities));
    }

    /**
     * @param PantherClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function login(Closure $callback)
    {
        $this->getClient()->request('GET', 'https://web.whatsapp.com/');
        $refBefore = '';
        do {
            try {
                $element = $this->getClient()->findElement(WebDriverBy::cssSelector('.landing-main [data-ref]'));
                $ref = $element->getAttribute('data-ref');
                if ($refBefore != $ref) {
                    $refBefore = $ref;
                    call_user_func($callback, $element->takeElementScreenshot(), $this);
                }
            } catch (\Exception $e) { }
            try {
                $menu = $this->getClient()->findElement(WebDriverBy::cssSelector('[data-testid="menu"][data-icon="menu"]'));
            } catch (\Exception $e) { }
            sleep(1);
        } while(empty($menu));
    }

    public function __destruct()
    {
        $this->getClient()->quit();
    }
}
