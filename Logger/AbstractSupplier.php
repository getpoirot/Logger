<?php
namespace Poirot\Logger\Logger;

use Poirot\Core\Entity;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Core\Traits\OptionsTrait;
use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Logger\Interfaces\Logger\iLogSupplier;

abstract class AbstractSupplier implements iLogSupplier
{
    use OptionsTrait;

    /** @var string[] Ignored Data From Log */
    protected $ignoreData = [];

    /**
     * Construct
     *
     * @param array|iDataSetConveyor $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
    }


    abstract protected function doSend(iDataSetConveyor $logData);

    /**
     * Send Message To Log Supplier
     *
     * @param iLogData $logData
     *
     * @return $this
     */
    function send(iLogData $logData)
    {
        # filter ignored data
        $ignr = $this->getIgnoredData();

        $data = $logData->toArray();
        foreach($data as $k => $v) {
            if (in_array($k, $ignr))
                unset($data[$k]);
        }

        $logData = new Entity($data);

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
