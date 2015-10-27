<?php
namespace Poirot\Logger\Logger;

use Poirot\Core\OpenOptions;
use Poirot\Logger\Interfaces\Logger\iLogData;

class LogDataContext extends OpenOptions
    implements iLogData
{
    protected $level;
    protected $message;

    protected $timestamp;

    /**
     * Set Level
     *
     * @param int $level
     *
     * @return $this
     */
    function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get Level
     *
     * @return int
     */
    function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Message Log
     *
     * @param string $message
     *
     * @return $this
     */
    function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    function getMessage()
    {
        return $this->message;
    }


    // ...

    /**
     * @return mixed
     */
    function getTimestamp()
    {
        if (!$this->timestamp)
            $this->timestamp = new \DateTime();

        return $this->timestamp;
    }
}
