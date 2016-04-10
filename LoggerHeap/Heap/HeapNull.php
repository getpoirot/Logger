<?php
namespace Poirot\Logger\LoggerHeap\Heap;

use Poirot\Logger\Interfaces\iContext;

class HeapNull extends aHeap
{
    protected function doWrite(iContext $logData)
    { }
}
