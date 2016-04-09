<?php
namespace Poirot\Logger\Context;

class ContextMemoryUsage extends aContext
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
     * @return ContextMemoryUsageOptions
     */
    function optsData()
    {
        return parent::optsData();
    }

    /**
     * @return ContextMemoryUsageOptions
     */
    static function newOptsData($builder = null)
    {
        return (new ContextMemoryUsageOptions)->from($builder);
    }
}
