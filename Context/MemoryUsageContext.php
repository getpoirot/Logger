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
        return memory_get_peak_usage($this->options()->getRealUsage());
    }


    // ...

    /**
     * @override autocomplete
     *
     * @return MemoryUsageOptions
     */
    function options()
    {
        return parent::options();
    }

    /**
     * @return MemoryUsageOptions
     */
    static function optionsIns()
    {
        return new MemoryUsageOptions;
    }
}
