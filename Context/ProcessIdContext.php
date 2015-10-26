<?php
namespace Poirot\Logger\Context;

class ProcessIdContext extends AbstractContext
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
