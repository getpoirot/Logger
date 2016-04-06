<?php
namespace Poirot\Logger\Interfaces\Logger;

use Poirot\Std\Interfaces\Struct\iDataStruct;

interface iLogSupplier
{
    /**
     * Send Message To Log Supplier
     *
     * @param iDataStruct $logData
     *
     * @return $this
     */
    function send(iDataStruct $logData);

    /**
     * Ignore Data Key From Log
     *
     * @param string $key
     *
     * @return $this
     */
    function ignoreData($key);

    /**
     * Ignore Bunch Of Data Keys From Log
     *
     * @param array $keys
     *
     * @return $this
     */
    function ignoreDataSet(array $keys);

    /**
     * Get Ignored Data
     *
     * @return string[]
     */
    function getIgnoredData();
}
