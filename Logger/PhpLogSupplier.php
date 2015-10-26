<?php
namespace Poirot\Logger\Logger;

use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Core\Traits\OptionsTrait;
use Poirot\Logger\Interfaces\iFormatter;
use Poirot\Logger\Interfaces\Logger\iFormatterProvider;
use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Logger\Interfaces\Logger\iLogSupplier;

class PhpLogSupplier
    implements iLogSupplier
    , iFormatterProvider
{
    use OptionsTrait;

    const MESSAGE_OS   = 0;
    const MESSAGE_SAPI = 4;

    /** @var iFormatter */
    protected $formatter;

    protected $messageType;

    /**
     * Construct
     *
     * @param array|iDataSetConveyor $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
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
            $this->messageType = self::MESSAGE_SAPI;

        return $this->messageType;
    }


    // ...

    /**
     * Send Message To Log Supplier
     *
     * @param iLogData $logData
     *
     * @return $this
     */
    function send(iLogData $logData)
    {
        if ($this->expandNewlines) {
            $lines = preg_split('{[\r\n]+}', (string) $record['formatted']);
            foreach ($lines as $line) {
                error_log($line, $this->messageType);
            }
        } else {
            error_log((string) $record['formatted'], $this->messageType);
        }
    }

    /**
     * Get Formatter
     *
     * @return iFormatter
     */
    function formatter()
    {
        if (!$this->formatter)
            $this->formatter;

        return $this->formatter;
    }
}
