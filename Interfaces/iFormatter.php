<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Std\Interfaces\Struct\iDataStruct;

interface iFormatter
{
    /**
     * Format Data To String
     *
     * TODO review iDataStruct
     * @param iDataStruct|iLogData $logData
     * @return string
     */
    function toString(iDataStruct $logData);
}
