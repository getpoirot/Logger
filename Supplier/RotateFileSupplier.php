<?php
namespace Poirot\Logger\Supplier;

use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Logger\Formatter\PsrLogMessageFormatter;
use Poirot\Logger\Interfaces\iFormatter;
use Poirot\Logger\Interfaces\Logger\iFormatterProvider;

class RotateFileSupplier extends AbstractSupplier
    implements iFormatterProvider
{
    # options
    protected $filePath;
    protected $filePermission = 0644;
    protected $_f__limitation;
    protected $_f__limitReach;

    /** @var iFormatter */
    protected $formatter;

    protected function doSend(iDataSetConveyor $logData)
    {
        $filePath = $this->getFilePath();
        if ($filePath === null)
            return;

        // check dir exists
        $fileDir = dirname($filePath);
        if (!is_dir($fileDir))
            mkdir($fileDir, 0755, true);

        file_put_contents(
            $filePath
            , $this->formatter()->toString($logData)."\r\n"
            , FILE_APPEND|LOCK_EX
        );
        chmod($filePath, $this->getFilePermission());
    }

    /**
     * Get Formatter
     *
     * @return PsrLogMessageFormatter|iFormatter
     */
    function formatter()
    {
        if (!$this->formatter)
            $this->formatter = new PsrLogMessageFormatter;

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
        if (defined('APP_DIR_TEMP') && !$this->filePath)
            $this->setFilePath(APP_DIR_TEMP.'/log/php_errors.log');

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
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * File Permission
     *
     * @return int
     */
    function getFilePermission()
    {
        return $this->filePermission;
    }

    /**
     * Set File Permission
     *
     * @param int $filePermission
     *
     * @return $this
     */
    function setFilePermission($filePermission)
    {
        $this->filePermission = $filePermission;

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
                return $this->__hasLimitationReach($filePath);
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
                $this->__doLimitationReach($filePath);
            });

        return $this->_f__limitReach;
    }

    // ...

    protected function __hasLimitationReach($filePath)
    {
        return (is_file($filePath) && filesize($filePath) > 50000);
    }

    protected function __doLimitationReach($filePath)
    {
        unlink($filePath);
    }
}
