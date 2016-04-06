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
        return memory_get_peak_usage($this->optsData()->getRealUsage());
    }


    // ...

    /**
     * @override autocomplete
     *
     * @return MemoryUsageOptions
     */
    function optsData()
    {
        return parent::optsData();
    }

    /**
     * @return MemoryUsageOptions
     */
    static function newOptsData($builder = null)
    {
        return (new MemoryUsageOptions)->from($builder);
    }
}
