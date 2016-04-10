<?php
namespace Poirot\Logger\LoggerHeap\Interfaces;

use Poirot\Logger\Interfaces\iContext;

/**
 * Heaps in LoggerHeap can attached as supplier for log mechanism
 * each heap can implement different way of logging mechanism.
 * @see LoggerHeap::attach
 */
interface iHeapLogger
{
    /**
     * Write Message From Heap To Log System
     *
     * @param iContext $logData
     *
     * @return $this
     */
    function write(iContext $logData);

    /**
     * Ignore Data Context Key From Log
     *
     * @param string $key
     *
     * @return $this
     */
    function setIgnoreData($key);

    /**
     * Ignore Bunch Of Data Context Keys From Log
     *
     * @param array $keys
     *
     * @return $this
     */
    function setIgnoreDataSet(array $keys);

    /**
     * Get Ignored Data
     *
     * @return string[]
     */
    function getIgnoredData();
}
