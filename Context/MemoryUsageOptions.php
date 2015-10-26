<?php
namespace Poirot\Logger\Context;

use Poirot\Core\AbstractOptions;

class MemoryUsageOptions extends AbstractOptions
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
