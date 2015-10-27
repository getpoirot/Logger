<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Logger\Interfaces\Logger\iLogData;

interface iFormatter
{
    /**
     * Format Data To String
     *
     * @param iDataSetConveyor|iLogData $logData
     * @return string
     */
    function toString(iDataSetConveyor $logData);
}
