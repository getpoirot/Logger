<?php
namespace Poirot\Logger\Context;

class ContextPID extends aContext
{
    /**
     * Get Current Process Id
     *
     * @return int
     */
    function getProcessId()
    {
        return getmypid();
    }
}
