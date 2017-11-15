<?php
namespace Poirot\Logger\LoggerHeap\Heap;

use \MongoDB\Collection;
use \MongoDB\BSON\UTCDateTime;

use Poirot\Std\Interfaces\Struct\iData;
use Poirot\Logger\Interfaces\iContext;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatter;
use Poirot\Logger\LoggerHeap\Interfaces\iFormatterProvider;
use Poirot\Logger\LoggerHeap\Formatter\FormatterPsrLogMessage;

class HeapMongoLog
    extends    aHeap
    implements iFormatterProvider
{

    /** @var  Collection */
    protected $collection;
    /** @var iFormatter */
    protected $formatter;
    /** @var  array */
    protected $logs = [];

    /**
     * Construct
     *
     * @param array|iData $options Options
     * @param Collection $collection
     */
    public function __construct(iData $options = null, Collection $collection)
    {
        parent::__construct($options);
        $this->collection = $collection;
    }

    /**
     * Surprisingly does NOT write!
     * Instead adds $logData to its internal array,to be inserted while __destruct
     * TODO: $this->formatter()->toString($logData)
     *
     * @param iContext $logData
     */
    protected function doWrite(iContext $logData)
    {
        $logData = \iterator_to_array($logData);
        /** @var \DateTime $datetime */
        $datetime = $logData['timestamp'];
        $log = [
            'datetime'      => new UTCDateTime ($datetime->getTimestamp() * 1000),
            'message'       => $logData['message'],
//            'psr_message'   => $this->formatter()->toString($logData),
            'level'         => $logData['level'],
        ];
        unset($logData['timestamp']); unset($logData['message']); unset($logData['level']);
        if ($logData)
            $log['context'] = $logData;
        $this->logs[] = $log;
    }

    /**
     * Get Formatter
     *
     * @return FormatterPsrLogMessage
     */
    function formatter()
    {
        if (!$this->formatter)
            $this->formatter = new FormatterPsrLogMessage;

        return $this->formatter;
    }

    /**
     * Actual logging happens here
     */
    public function __destruct()
    {
        try {
            if ($this->logs) {
                $this->collection->insertMany($this->logs, [ 'ordered' => false ]);
            }
        } catch (\Exception $exception) {}
    }
}