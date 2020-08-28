<?php
namespace WhatsappClient\webwhatsapi;

class Wrapper extends JSAdapter
{
    /**
     * @var PantherClient
     */
    private $client;
    /**
     * Available functions
     * 
     * @var array
     */
    private $availableFunctions = [];
    public function __construct($client)
    {
        $this->client = $client;
        $this->loadWapi();
    }

    private function loadWapi()
    {
        $this->availableFunctions = $this->client->executeScript(
            file_get_contents(__DIR__.'/js/wsapi.js')."\n".
            'return Object.keys(window.WAPI);'
        );
    }

    public function __call($method, $arguments)
    {
        array_walk($arguments, function(&$arg) {
            $arg = is_string($arg) ? "'$arg'" : $arg;
        });
        $arguments[] = 'arguments[0]';
        return $this->client->executeAsyncScript(
            'return WAPI.' . $method . '(' . implode(',', $arguments) . ')'
        );
    }

    public function getAvailableFunctions()
    {
        return $this->availableFunctions;
    }

    public function getProfilePicFromId($id)
    {
        $profile_pic = $$this->__call('getProfilePicFromId', $id);
        if ($profile_pic) {
            return base64_decode($profile_pic);
        }
        return false;
    }
}