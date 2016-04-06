<?php
namespace Poirot\Logger\Supplier;

use Poirot\Std\Interfaces\Struct\iDataStruct;

class NullSupplier extends AbstractSupplier
{
    protected function doSend(iDataStruct $logData)
    { }
}
