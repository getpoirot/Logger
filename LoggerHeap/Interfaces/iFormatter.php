<?php
namespace Poirot\Logger\LoggerHeap\Interfaces;

use Poirot\Logger\Interfaces\iContext;

/**
 * Some HeapLogger may used Formatter to format data context
 * as string and prepare it for write to log mechanism
 */
interface iFormatter
{
    /**
     * Format Data Context To String
     *
     * @param iContext $logData
     *
     * @return string
     */
    function toString(iContext $logData);
}
