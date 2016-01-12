<?php
namespace Poirot\Logger\Context;

class MemoryUsageContext extends AbstractContext
{
    /**
     * Get Memory Usage Peak
     *
     * @return int
     */
    function getMemoryUsage()
    {
        return memory_get_peak_usage($this->inOptions()->getRealUsage());
    }


    // ...

    /**
     * @override autocomplete
     *
     * @return MemoryUsageOptions
     */
    function inOptions()
    {
        return parent::options();
    }

    /**
     * @return MemoryUsageOptions
     */
    static function newOptions()
    {
        return new MemoryUsageOptions;
    }
}
