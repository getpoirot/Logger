<?php
namespace Poirot\Logger\Interfaces;

interface iLoggerProvider
{
    /**
     * Get Logger
     *
     * @return iLogger
     */
    function logger();
}
