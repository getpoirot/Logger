<?php
namespace Poirot\Logger\Context;

use Poirot\Std\Struct\aDataOptions;

class ContextMemoryUsageOptions extends aDataOptions
{
    protected $realUsage = true;

    function getRealUsage()
    {
        return $this->realUsage;
    }

    function setRealUsage($realUsage)
    {
        $this->realUsage = (bool) $realUsage;
    }
}
