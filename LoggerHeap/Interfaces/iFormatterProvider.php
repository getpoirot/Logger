<?php
namespace Poirot\Logger\LoggerHeap\Interfaces;

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
