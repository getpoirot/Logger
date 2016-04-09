<?php
namespace Poirot\Logger\Supplier;

use Poirot\Std\Interfaces\Struct\iData;

class SupplierNull extends aSupplierLogger
{
    protected function doSend(iData $logData)
    { }
}
