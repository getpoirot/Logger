<?php
namespace Poirot\Logger\Context;

use Poirot\Std\Struct\AbstractOptionsData;

class MemoryUsageOptions extends AbstractOptionsData
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
