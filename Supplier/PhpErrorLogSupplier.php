<?php
namespace Poirot\Logger\Supplier;

use Poirot\Logger\Formatter\PsrLogMessageFormatter;
use Poirot\Logger\Interfaces\iFormatter;
use Poirot\Logger\Interfaces\Logger\iFormatterProvider;
use Poirot\Std\Interfaces\Struct\iDataStruct;

class PhpErrorLogSupplier extends AbstractSupplier
    implements iFormatterProvider
{
    const DEFAULT_TEMPLATE = '({level}): {message}, [{%}]';

    ## using address set on error_log in php.ini
    const MESSAGE_OS   = 0;
    const MESSAGE_SAPI = 4;

    /** @var iFormatter */
    protected $formatter;

    // options
    protected $messageType;
    protected $expandNewLines;

    /**
     * Construct
     *
     * @param array|iDataStruct $options Options
     */
    function __construct($options = null)
    {
        parent::__construct($options);

        ## php error_log will append timestamp so we don`t need it anymore in template
        $this->ignoreData('timestamp');
    }

    protected function doSend(iDataStruct $logData)
    {
        $formattedString = $this->formatter()->toString($logData);

        $lines = ($this->getExpandNewLines())
            ? preg_split('{[\r\n]+}', $formattedString)
            : [$formattedString];

        foreach ($lines as $line)
            error_log($line, $this->getMessageType());
    }

    /**
     * Get Formatter
     *
     * @return PsrLogMessageFormatter|iFormatter
     */
    function formatter()
    {
        if (!$this->formatter)
            ## php error_log will append timestamp so we don`t need it anymore in template
            $this->formatter = new PsrLogMessageFormatter(['template' => self::DEFAULT_TEMPLATE]);

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
        if (!$this->messageType)
            $this->messageType = self::MESSAGE_OS;

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
    function getExpandNewLines()
    {
        return $this->expandNewLines;
    }
}
