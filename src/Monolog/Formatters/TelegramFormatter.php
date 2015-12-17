<?php

namespace PrivateDev\Monolog\Formatters\TelegramFormatter;

use Monolog\Formatter\LineFormatter;

class TelegramFormatter extends LineFormatter
{
    /**
     * Needle trace phrase
     */
    const TRACE_STR = 'Stack trace';

    /**
     * TelegramFormatter constructor.
     *
     * @param null|string $format
     * @param null|string $dateFormat
     * @param bool        $allowInlineLineBreaks
     * @param bool        $ignoreEmptyContextAndExtra
     * @param bool        $includeStacktraces           Switch ON/OFF full stack trace
     */
    public function __construct(
        $format,
        $dateFormat,
        $allowInlineLineBreaks,
        $ignoreEmptyContextAndExtra,
        $includeStacktraces = true
    ) {
        $this->includeStacktraces = $includeStacktraces;

        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    /**
     * @param array $record
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        $output = parent::format($record);

        if (! $this->includeStacktraces) {
            $output = $this->cutTrace($output);
        }

        return $output;
    }

    /**
     * Cut trace from message
     *
     * @param $message
     * @return string
     */
    protected function cutTrace($message)
    {
        $tracePosition = strpos($message, self::TRACE_STR);

        if ($tracePosition) {
            $message = substr($message, 0, $tracePosition);
        }

        return $message;
    }
}
