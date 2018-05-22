<?php
namespace Poirot\Logger\LoggerHeap\Heap;

use Poirot\Logger\Interfaces\iContext;
use Poirot\Logger\LoggerHeap\Formatter\FormatterPsrLogMessage;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatter;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatterProvider;


class HeapFileRotate
    extends    aHeap
    implements iFormatterProvider
{
    # options
    protected $filePath;
    protected $filePermission = 0644;
    /** @var callable */
    protected $_f__limitation; # determine limitation
    protected $_f__limitReach; # determine limitation reach

    /** @var iFormatter */
    protected $formatter;

    protected function doWrite(iContext $logData)
    {
        $filePath = $this->getFilePath();
        if ($filePath === null)
            ## nothing to do file log not defined
            #!# in log we prefer to not throw any error message
            return;

        // check dir exists
        $fileDir = dirname($filePath);
        if (!is_dir($fileDir))
            mkdir($fileDir, 0755, true);

        $contents = $this->formatter()->toString($logData)."\r\n";
        file_put_contents(
            $filePath
            , $contents
            , FILE_APPEND|LOCK_EX
        );
    }

    /**
     * Get Formatter
     *
     * @return FormatterPsrLogMessage|iFormatter
     */
    function formatter()
    {
        if (!$this->formatter)
            $this->formatter = new FormatterPsrLogMessage;

        return $this->formatter;
    }

    /**
     * Shutdown
     *
     */
    function __destruct()
    {
        try {
            if (call_user_func($this->getLimitation(), $this->getFilePath()))
                call_user_func($this->getLimitReach(), $this->getFilePath());
        } catch (\Exception $e)
        { }
    }


    // options:

    /**
     * Get Path Filename Of Log File
     *
     * @return string
     */
    function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set Path Filename Of Log File
     *
     * @param string $filePath
     *
     * @return $this
     */
    function setFilePath($filePath)
    {
        $this->filePath = (string) $filePath;
        return $this;
    }

    /**
     * Set Callable To Check When To Rotate File (Limitation Reach)
     *
     * @param callable $limitation
     * : callable function($filePath)
     *
     * @return $this
     */
    function setLimitation(callable $limitation)
    {
        $this->_f__limitation = $limitation;
        return $this;
    }

    /**
     * Get Callable To Check When To Rotate File (Limitation Reach)
     *
     * @return callable
     */
    function getLimitation()
    {
        if (!$this->_f__limitation)
            $this->setLimitation(function($filePath) {
                return $this->_default_hasLimitationReach($filePath);
            });

        return $this->_f__limitation;
    }

    /**
     * Set Callable To Rotate Current File That Meet Limitation
     *
     * - by default the file will be deleted
     *
     * @param callable $limitReach
     * : callable function($filePath)
     *
     * @return $this
     */
    function setLimitReach($limitReach)
    {
        $this->_f__limitReach = $limitReach;
        return $this;
    }

    /**
     * Get Callable To Rotate Current File That Meet Limitation
     *
     * - by default the file will be deleted
     *
     * @return callable
     */
    function getLimitReach()
    {
        if (!$this->_f__limitReach)
            $this->setLimitReach(function($filePath) {
                $this->_default_doLimitationReach($filePath);
            });

        return $this->_f__limitReach;
    }

    // ...

    protected function _default_hasLimitationReach($filePath)
    {
        return (is_file($filePath) && filesize($filePath) > 50000);
    }

    protected function _default_doLimitationReach($filePath)
    {
        unlink($filePath);
    }
}
