<?php

namespace PrivateDev\Monolog\Exceptions\TelegramException;

class TelegramException extends \Exception
{
    protected $message = ' #Telegram Bot API: https://core.telegram.org/bots/api';
    protected $code;

    public function __construct($message, $code, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = $message . $this->message;
    }
}
