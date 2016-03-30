<?php

namespace PrivateDev\Monolog\Handlers;

use Monolog\Logger;

/**
 * Allows you to send log messages to Slack channel
 *
 * Class SlackHandler
 *
 * @package PrivateDev\Monolog\Handlers
 */
class SlackHandler extends AbstractMessengerHandler
{
    /**
     * @var string
     */
    protected $webhook;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var string
     */
    protected $username;

    /**
     * SlackHandler constructor.
     *
     * @param int       $webhook  Slack webhook url
     * @param string    $channel  Slack channel name
     * @param string    $username Sender name
     * @param int       $level
     * @param bool|true $bubble
     */
    public function __construct($webhook, $channel, $username, $level = Logger::ERROR, $bubble = true)
    {
        $this->webhook = $webhook;
        $this->channel = $channel;
        $this->username = $username;

        parent::__construct($level, $bubble);
    }

    protected function getUrl()
    {
        return $this->webhook;
    }

    protected function makeRequestData($content)
    {
        $data = [
            'channel'  => $this->channel,
            'username' => $this->username,
        ];

        if (isset($content['formatted']['pretext'])) {
            $data['attachments'] = [$content['formatted']];
        } else {
            $data['text'] = $content['formatted'];
        }

        return $data;
    }
}
