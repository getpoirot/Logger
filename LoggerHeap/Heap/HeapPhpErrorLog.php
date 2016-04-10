<?php
namespace Poirot\Logger\LoggerHeap\Heap;

use Poirot\Logger\Interfaces\iContext;
use Poirot\Logger\LoggerHeap\Formatter\FormatterPsrLogMessage;
use Poirot\Std\Interfaces\Struct\iData;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatter;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatterProvider;

class HeapPhpErrorLog
    extends    aHeap
    implements iFormatterProvider
{
    const DEFAULT_TEMPLATE = '({level}): {message}, [{%}]';

    ## using address set on error_log in php.ini
    const MESSAGE_OS   = 0;
    const MESSAGE_SAPI = 4;

    /** @var iFormatter */
    protected $formatter;

    // options
    protected $messageType = self::MESSAGE_OS;
    protected $expandNewLines = false;

    /**
     * Construct
     *
     * @param array|iData $options Options
     */
    function __construct($options = null)
    {
        parent::__construct($options);

        ## php error_log will append timestamp so we don`t need it anymore in template
        $this->setIgnoreData('timestamp');
    }

    protected function doWrite(iContext $logData)
    {
        $formattedString = $this->formatter()->toString($logData);

        $lines = ($this->isExpandNewLines())
            ? preg_split('{[\r\n]+}', $formattedString)
            : [$formattedString];

        foreach ($lines as $line)
            error_log($line, $this->getMessageType());
    }

    /**
     * Get Formatter
     *
     * @return FormatterPsrLogMessage|iFormatter
     */
    function formatter()
    {
        if (!$this->formatter)
            ## php error_log will append timestamp so we don`t need it anymore in template
            $this->formatter = new FormatterPsrLogMessage(['template' => self::DEFAULT_TEMPLATE]);

        return $this->formatter;
    }


    // Log Options:

    /**
     * Says where the error should go
     *
     * @param int $type
     *
     * @return $this
     */
    function setMessageType($type)
    {
        if (!in_array($type, [self::MESSAGE_SAPI, self::MESSAGE_OS]))
            throw new \InvalidArgumentException(sprintf(
                'The given message type (%s) is not supported.'
                , $type
            ));

        $this->messageType = $type;
        return $this;
    }

    /**
     * Get Message Type
     *
     * @return int
     */
    function getMessageType()
    {
        return (int) $this->messageType;
    }

    /**
     * Expand each new lines chars to new log message
     *
     * @param bool $expandNewLines
     * @return $this
     */
    function setExpandNewLines($expandNewLines)
    {
        $this->expandNewLines = (bool) $expandNewLines;
        return $this;
    }

    /**
     * @return bool
     */
    function isExpandNewLines()
    {
        return $this->expandNewLines;
    }
}
