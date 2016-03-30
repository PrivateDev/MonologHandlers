<?php

namespace PrivateDev\Monolog\Handlers;

use Monolog\Logger;

/**
 * Allows you to send log messages to Telegram messenger via Telegram Bot.
 * Bot ID and channel/chat ID are required
 *
 * Class TelegramHandler
 *
 * @package PrivateDev\Monolog\Handlers
 */
class TelegramHandler extends AbstractMessengerHandler
{
    /**
     * Bot API url
     */
    const BOT_URL = 'https://api.telegram.org/bot';

    const TEXT_FIELD = 'text';
    const CHAT_ID_FIELD = 'chat_id';
    const SEND_MESSAGE_METHOD = 'sendMessage';
    const MAX_LENGTH = 4096; //Current maximum length is 4096 UTF8 characters

    private $chatId;
    private $botToken;

    /**
     * TelegramHandler constructor.
     *
     * @param int       $botToken Bot token. More info: https://core.telegram.org/bots/api#authorizing-your-bot
     * @param bool      $chatId   Chat or Channel ID to send message. '@username' is possible too
     * @param int       $level
     * @param bool|true $bubble
     */
    public function __construct($botToken, $chatId, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    protected function getUrl()
    {
        return $this->getBaseUrl() . self::SEND_MESSAGE_METHOD;
    }

    /**
     * Base required url
     *
     * @return string
     */
    private function getBaseUrl()
    {
        return self::BOT_URL . $this->botToken . '/';
    }

    protected function makeRequestData($content)
    {
        return [
            self::CHAT_ID_FIELD => $this->chatId,
            self::TEXT_FIELD    => $content,
        ];
    }
}
