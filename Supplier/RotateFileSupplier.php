<?php
namespace Poirot\Logger\Logger;

use Poirot\Core\Interfaces\iDataSetConveyor;

class RotateFileSupplier extends AbstractSupplier
{
    # options
    protected $filePath;
    protected $filePermission = 0644;
    protected $_f__limitation;
    protected $limitReach;


    protected function doSend(iDataSetConveyor $logData)
    {
        // TODO: Implement doSend() method.
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
     *
     * @return $this
     */
    function setLimitation(callable $limitation)
    {
        $this->_f__limitation = $limitation;

        return $this;
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
        $this->limitReach = $limitReach;

        return $this;
    }
}
