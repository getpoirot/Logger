<?php
namespace Poirot\Logger\Interfaces\Logger;

interface iLogSupplier
{
    /**
     * Send Message To Log Supplier
     *
     * @param iLogData $logData
     *
     * @return $this
     */
    function send(iLogData $logData);
}
