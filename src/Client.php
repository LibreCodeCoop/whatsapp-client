<?php

namespace WhatsappClient;

use Closure;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCapabilities;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Symfony\Component\Panther\Client as PantherClient;
use WhatsappClient\webwhatsapi\Wrapper;

class Client
{
    /**
     * @var PantherClient
     */
    private $client;
    private $sessionFile;
    private $qrcodeCallback;
    private $logged = false;
    private $running = false;
    /**
     * @var Wrapper
     */
    public $WsapiWrapper;
    /**
     * @var Logger
     */
    protected $logger;
    public function __construct($host, ?WebDriverCapabilities $capabilities = null, $sessionFile = '.session')
    {
        if (!$capabilities) {
            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        }
        $this->sessionFile = $sessionFile;
        $this->setClient(PantherClient::createSeleniumClient($host, $capabilities));
        $this->enableLog();
    }

    public function enableLog(array $settings = [])
    {
        $defaultSettings = [
            'name' => 'WHATSAPP',
            'handlers' => [
                new StreamHandler($_ENV['LOG_DIR'] . '/log.log'),
                new SyslogHandler(true)
            ],
            'enable_error_handler' => true
        ];
        $settings = array_merge($defaultSettings, $settings);
        if ($settings['logger']) {
            $this->logger = $settings['logger'];
        } else {
            $this->logger = new Logger($settings['name']);
        }
        foreach ($settings['handlers'] as $handler) {
            $this->logger->pushHandler($handler);
        }
        if ($settings['enable_error_handler']) {
            $handler = new \Monolog\ErrorHandler($this->logger);
            $handler->registerErrorHandler([], false);
            $handler->registerExceptionHandler();
            $handler->registerFatalHandler();
        }
        return $this->logger;
    }

    /**
     * @param PantherClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setQrcodeCallback($closure)
    {
        $this->qrcodeCallback = $closure;
    }

    public function sessionStart()
    {
        if (!$this->loadSessionFromFile()) {
            $this->login();
        }
        $this->WsapiWrapper = new Wrapper($this->client);
    }

    public function loop($callback)
    {
        $this->running = true;
        while ($this->running) {
            $jsMessages = $this->WsapiWrapper->getBufferedNewMessages();
            if ($jsMessages) {
                $callback($this, $jsMessages);
            }
            sleep(2);
        }
    }

    private function loadSessionFromFile()
    {
        if (!file_exists($this->sessionFile)) {
            $this->logger->info('Session file does not exist: ' . $this->sessionFile);
            return false;
        }
        $json = file_get_contents($this->sessionFile);
        if (!$json) {
            $this->logger->info('Empty session file: ' . $this->sessionFile);
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
        do {
            $noPopup = null;
            try {
                $menu = $this->client->findElement(WebDriverBy::cssSelector('[data-testid="menu"][data-icon="menu"]'));
            } catch (\Exception $e) {
                try {
                    $popup = $this->client->findElement(WebDriverBy::cssSelector('[data-animate-modal-body="true"]'));
                    $errormsg = explode("\n", $popup->getText())[0];
                    $this->logger->info('Failure on load session: ' . $errormsg);
                } catch (\Exception $noPopup) {
                }
            }
        } while (!empty($noPopup) || $errormsg);
        if (empty($menu)) {
            $this->logger->info('Invalid session');
            return false;
        }
        $this->logger->info('Session loaded');
        return true;
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
                        call_user_func_array($this->qrcodeCallback, [$element->takeElementScreenshot(), $this]);
                    }
                }
            } catch (\Exception $e) {
            }
            try {
                $menu = $this->client->findElement(WebDriverBy::cssSelector('[data-testid="menu"][data-icon="menu"]'));
            } catch (\Exception $e) {
            }
            sleep(1);
        } while (empty($menu));
    }

    public function __destruct()
    {
        $json = $this->client->executeScript('return localStorage');
        file_put_contents($this->sessionFile, json_encode($json));
        $this->client->quit();
    }
}
