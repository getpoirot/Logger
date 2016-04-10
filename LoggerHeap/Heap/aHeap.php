<?php
namespace Poirot\Logger\LoggerHeap\Heap;

use Poirot\Logger\Interfaces\iContext;
use Poirot\Logger\LoggerHeap\Interfaces\iHeapLogger;
use Poirot\Std\Struct\aDataOptions;

abstract class aHeap
    extends    aDataOptions
    implements iHeapLogger
{
    /** @var string[] Ignored Data From Log */
    protected $ignoreData = [];


    abstract protected function doWrite(iContext $logData);

    /**
     * Send Message To Log Heap
     *
     * @param iContext $logData
     *
     * @return $this
     */
    function write(iContext $logData)
    {
        $logData = clone $logData;

        # filter ignored data
        $ignr = $this->getIgnoredData();

        foreach($logData as $k => $v) {
            if (in_array($k, $ignr))
                $logData->del($k);
        }

        // ...

        $this->doWrite($logData);
    }

    /**
     * Ignore Data Key From Log
     *
     * @param string $key
     *
     * @return $this
     */
    function setIgnoreData($key)
    {
        $this->ignoreData[$key] = true;
        return $this;
    }

    /**
     * Ignore Bunch Of Data Keys From Log
     *
     * @param array $keys
     *
     * @return $this
     */
    function setIgnoreDataSet(array $keys)
    {
        foreach($keys as $key)
            $this->setIgnoreData($key);

        return $this;
    }

    /**
     * Get Ignored Data
     *
     * @return string[]
     */
    function getIgnoredData()
    {
        return array_keys($this->ignoreData);
    }
}
