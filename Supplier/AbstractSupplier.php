<?php
namespace Poirot\Logger\Supplier;

use Poirot\Logger\Interfaces\Logger\iLogSupplier;
use Poirot\Std\Interfaces\Struct\iDataStruct;
use Poirot\Std\Struct\AbstractOptionsData;

abstract class AbstractSupplier
    extends AbstractOptionsData
    implements iLogSupplier
{
    /** @var string[] Ignored Data From Log */
    protected $ignoreData = [];


    abstract protected function doSend(iDataStruct $logData);

    /**
     * Send Message To Log Supplier
     *
     * @param iDataStruct $logData
     *
     * @return $this
     */
    function send(iDataStruct $logData)
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
