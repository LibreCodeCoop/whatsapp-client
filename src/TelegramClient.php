<?php

namespace WhatsappClient;

use Facebook\WebDriver\WebDriverCapabilities;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class TelegramClient extends Client
{
    public function __construct($host, ?WebDriverCapabilities $capabilities = null, $sessionFile = '.session')
    {
        parent::__construct($host, $capabilities, $sessionFile);
        $this->setQrcodeCallback([$this, 'qrcodeCallback']);
    }

    public function qrcodeCallback($screenshot, $client)
    {
        $telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);

        $inputFile = new InputFile();
        $inputFile->setFilename('qrcode.png');
        $inputFile->setContents($this->addBorderToImageString($screenshot));

        static $messageId;
        if ($messageId) {
            $telegram->deleteMessage([
                'chat_id' => $_ENV['TELEGRAM_CHAT_ID'],
                'message_id' => $messageId,
            ]);
        }

        $response = $telegram->sendPhoto([
            'chat_id' => $_ENV['TELEGRAM_CHAT_ID'], 
            'photo' => $inputFile
        ]);
        $messageId = $response->getMessageId();
    }

    private function addBorderToImageString($imageString, $border = 30)
    {
        $gd = imagecreatefromstring($imageString);
        $width=ImageSx($gd);
        $height=ImageSy($gd);
        $img_adj_width=$width+(2*$border);
        $img_adj_height=$height+(2*$border);
        $newimage=imagecreatetruecolor($img_adj_width,$img_adj_height);
        $border_color = imagecolorallocate($newimage, 255, 255, 255);
        imagefilledrectangle($newimage,0,0,$img_adj_width,$img_adj_height,$border_color); 
        imageCopyResized($newimage,$gd,$border,$border,0,0,$width,$height,$width,$height);
        ob_start();
        imagepng($newimage);
        $image_data = ob_get_contents();
        ob_end_clean();
        return $image_data;
    }

}