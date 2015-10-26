<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Logger\Interfaces\Logger\iLogData;

interface iFormatter
{
    /**
     * Format Data To String
     *
     * @param iLogData $logData
     *
     * @return string
     */
    function format(iLogData $logData);
}
