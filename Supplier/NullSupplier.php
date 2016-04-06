<?php
namespace Poirot\Logger\Supplier;

use Poirot\Std\Interfaces\Struct\iData;

class NullSupplier extends Supplier
{
    protected function doSend(iData $logData)
    { }
}
