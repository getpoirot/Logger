<?php
namespace Poirot\Logger\Interfaces\Logger;

use Poirot\Logger\Interfaces\iFormatter;

/**
 * Used On Logger(iLogger) that must implement Formatter
 */
interface iFormatterProvider
{
    /**
     * Get Formatter
     *
     * @return iFormatter
     */
    function formatter();
}
