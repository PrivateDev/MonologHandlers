<?php

namespace PrivateDev\Monolog\Formatters;

use Monolog\Formatter\LineFormatter;

class CustomLineFormatter extends LineFormatter
{
    /**
     * Needle trace phrase
     */
    protected $traceStartString;

    /**
     * @var bool|true
     */
    protected $editFormatted;

    /**
     * CustomLineFormatter constructor.
     *
     * @param null|string $format
     * @param null|string $dateFormat
     * @param bool        $allowInlineLineBreaks
     * @param bool        $ignoreEmptyContextAndExtra
     * @param bool|true   $editFormatted    Switch ON/OFF full stack trace
     * @param string      $traceStartString String you trace starts from
     */
    public function __construct(
        $format,
        $dateFormat,
        $allowInlineLineBreaks,
        $ignoreEmptyContextAndExtra,
        $editFormatted = true,
        $traceStartString = 'Stack trace'
    ) {
        $this->editStackTrace = $editFormatted;
        $this->traceStartString = $traceStartString;

        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    /**
     * @param array $record
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        $output = parent::format($record);
        if ($this->editFormatted) {
            $output = $this->editFormatted($output);
        }

        return $output;
    }

    /**
     * Modify formatted message
     *
     * @param $message
     * @return string
     */
    protected function editFormatted($message)
    {
        $tracePosition = strpos($message, $this->traceStartString);

        if ($tracePosition) {
            $message = substr($message, 0, $tracePosition);
        }

        return $message;
    }
}
