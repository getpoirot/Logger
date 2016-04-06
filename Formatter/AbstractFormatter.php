<?php
namespace Poirot\Logger\Formatter;

use Poirot\Logger\Interfaces\iFormatter;
use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Std\Interfaces\Struct\iDataStruct;
use Poirot\Std\SetterBuilder;

abstract class AbstractFormatter extends SetterBuilder
    implements iFormatter
{
    /**
     * Default format specifier for DateTime objects is ISO 8601
     *
     * @see http://php.net/manual/en/function.date.php
     */
    const DEFAULT_DATETIME_FORMAT = 'c';

    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;

    /**
     * Format Data To String
     *
     * @param iDataStruct|iLogData $logData
     * @return string
     */
    abstract function toString(iDataStruct $logData);

    // ..

    function getDateTimeFormat()
    {
        if (!$this->dateTimeFormat)
            $this->setDateTimeFormat(self::DEFAULT_DATETIME_FORMAT);

        return $this->dateTimeFormat;
    }

    function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }

    // Tools:

    /**
     * Normalize all non-scalar data types (except null) in a string value
     * to represent the messages that must be log
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function flatten($value)
    {
        if (is_scalar($value) || null === $value)
            return $value;

        // better readable JSON
        static $jsonFlags;
        if ($jsonFlags === null) {
            $jsonFlags = 0;
            $jsonFlags |= defined('JSON_UNESCAPED_SLASHES') ? JSON_UNESCAPED_SLASHES : 0;
            $jsonFlags |= defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
        }

        if ($value instanceof \DateTime)
            $value = $value->format($this->getDateTimeFormat());
        elseif ($value instanceof \Traversable)
            $value = json_encode(iterator_to_array($value), $jsonFlags);
        elseif (is_array($value))
            $value = json_encode($value, $jsonFlags);
        elseif (is_object($value) && !method_exists($value, '__toString'))
            $value = sprintf('object(%s) %s', get_class($value), json_encode($value));
        elseif (is_resource($value))
            $value = sprintf('resource(%s)', get_resource_type($value));
        elseif (!is_object($value))
            $value = gettype($value);

        return (string) $value;
    }
}
