<?php

namespace PrivateDev\Monolog\Exceptions;

use Exception;

class MessengerHandlerException extends \Exception
{
    protected $message = "AbstractMessengerHandler response error. Check your Handler params.\n";

    public function __construct($message, $code)
    {
        $this->message .= $message;
        $this->code = $code;
    }
}