<?php

namespace PrivateDev\Monolog\Handlers\TelegramHandler;

use Monolog\Logger;
use Monolog\Handler\MailHandler;
use PrivateDev\Monolog\Exceptions\TelegramException\TelegramException;

class TelegramHandler extends MailHandler
{
    /**
     * Bot API url
     */
    const BOT_URL = 'https://api.telegram.org/bot';

    const TEXT_FIELD          = 'text';
    const CHAT_ID_FIELD       = 'chat_id';
    const SEND_MESSAGE_METHOD = 'sendMessage';
    const MAX_LENGTH          = 4096; //Current maximum length is 4096 UTF8 characters

    private $chatId;
    private $botToken;

    /**
     * TelegramHandler constructor.
     *
     * @param int $botToken     Bot token. More info: https://core.telegram.org/bots/api#authorizing-your-bot
     * @param bool $chatId      Chat or Channel ID to send message. '@username' is possible too
     * @param int $level
     * @param bool|true $bubble
     */
    public function __construct($botToken, $chatId, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->botToken = $botToken;
        $this->chatId   = $chatId;
    }

    /**
     * Send Log message to telegram chat/channel
     *
     * @param string $content
     * @param array $records
     */
    protected function send($content, array $records)
    {
        $ch = $this->makeCh($content);

        $result = curl_exec($ch);
        curl_close($ch);

        $this->checkResult($result);
    }

    /**
     * Prepare data for curl_exec
     *
     * @param $content
     * @return resource
     */
    private function makeCh($content)
    {
        $content = substr($content, 0, self::MAX_LENGTH);

        $url = $this->getNeedleUrl(self::SEND_MESSAGE_METHOD);
        $data = json_encode([
            self::CHAT_ID_FIELD => $this->chatId,
            self::TEXT_FIELD    => $content,
        ]);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    /**
     * Check sendMessage response
     *
     * @param $data
     * @throws TelegramException
     */
    private function checkResult($data)
    {
        $response = json_decode($data, true);
        $status   = filter_var($response['ok'], FILTER_VALIDATE_BOOLEAN);

        if (!$status) {
            throw new TelegramException($response['description'], $response['error_code']);
        }
    }


    /**
     * Get url for your method
     *
     * @param $needle
     * @return string
     */
    private function getNeedleUrl($needle)
    {
        return $this->getBaseUrl() . $needle;
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
}
