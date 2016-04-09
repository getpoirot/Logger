<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Std\Interfaces\Struct\iData;

interface iFormatter
{
    /**
     * Format Data To String
     *
     * @param iData|iLogData $logData
     * @return string
     */
    function toString(iData $logData);
}
