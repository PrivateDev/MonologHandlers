<?php

namespace PrivateDev\Monolog\Formatters;

use Monolog\Logger;

/**
 * Custom formatter for Slack. Allows to add stack trace as attachment
 *
 * Class SlackFormatter
 *
 * @package PrivateDev\Monolog\Formatters
 */
class SlackFormatter extends CustomLineFormatter
{
    protected $attachment;

    /**
     * SlackFormatter constructor.
     *
     * @param string    $format
     * @param string    $dateFormat
     * @param bool|true $attachment
     * @param string    $traceStartString
     */
    public function __construct(
        $format,
        $dateFormat,
        $attachment = true,
        $traceStartString
    ) {
        $this->attachment = $attachment;
        $removeStackTrace = ! $attachment;
        parent::__construct($format, $dateFormat, true, true, $removeStackTrace, $traceStartString);
    }

    public function format(array $record)
    {
        if ($this->attachment) {
            return $this->editFormatted($record);
        }

        return parent::format($record);
    }

    protected function editFormatted($record)
    {
        list($formatted['pretext'], $text) = explode($this->traceStartString, $record['message']);

        $formatted['title'] = $this->traceStartString;
        $formatted['text'] = '```' . $text . '```';
        $formatted['mrkdwn_in'] = ['text'];
        $formatted['color'] = $this->getAttachmentColor($record['level']);

        return $formatted;
    }

    /**
     * Returned a Slack message attachment color associated with
     * provided level.
     *
     * @param  int $level
     * @return string
     */
    protected function getAttachmentColor($level)
    {
        switch (true) {
            case $level >= Logger::ERROR:
                return 'danger';
            case $level >= Logger::WARNING:
                return 'warning';
            case $level >= Logger::INFO:
                return 'good';
            default:
                return '#e3e4e6';
        }
    }
}
