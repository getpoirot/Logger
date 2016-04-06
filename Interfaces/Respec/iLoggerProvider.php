<?php
namespace Poirot\Logger\Interfaces\Respec;

use Poirot\Logger\Interfaces\iLogger;

interface iLoggerProvider
{
    /**
     * Get Logger
     *
     * @return iLogger
     */
    function logger();
}
