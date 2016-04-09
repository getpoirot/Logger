<?php
namespace Poirot\Logger\Supplier;

use Poirot\Logger\Interfaces\Logger\iSupplierLogger;
use Poirot\Std\Interfaces\Struct\iData;
use Poirot\Std\Struct\aDataOptions;

abstract class aSupplierLogger
    extends aDataOptions
    implements iSupplierLogger
{
    /** @var string[] Ignored Data From Log */
    protected $ignoreData = [];


    abstract protected function doSend(iData $logData);

    /**
     * Send Message To Log Supplier
     *
     * @param iData $logData
     *
     * @return $this
     */
    function send(iData $logData)
    {
        $logData = clone $logData;

        # filter ignored data
        $ignr = $this->getIgnoredData();

        foreach($logData as $k => $v) {
            if (in_array($k, $ignr))
                $logData->del($k);
        }

        // ...

        $this->doSend($logData);
    }

    /**
     * Ignore Data Key From Log
     *
     * @param string $key
     *
     * @return $this
     */
    function ignoreData($key)
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
    function ignoreDataSet(array $keys)
    {
        foreach($keys as $key)
            $this->ignoreData($key);

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
