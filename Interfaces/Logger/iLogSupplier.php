<?php
namespace Poirot\Logger\Interfaces\Logger;

use Poirot\Std\Interfaces\Struct\iData;

interface iLogSupplier
{
    /**
     * Send Message To Log Supplier
     *
     * @param iData $logData
     *
     * @return $this
     */
    function send(iData $logData);

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
