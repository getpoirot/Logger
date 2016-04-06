<?php
namespace Poirot\Logger\Context;

use Poirot\Logger\Interfaces\iContext;
use Poirot\Std\Interfaces\Pact\ipOptionsProvider;
use Poirot\Std\Interfaces\Struct\iOptionsData;
use Poirot\Std\Struct\OpenOptionsData;

abstract class AbstractContext
    extends    OpenOptionsData // Use Setter/Getter On Extended Classes
    implements iContext
    , ipOptionsProvider
{
    /** @var string Context Name */
    protected $name;
    /** @var iOptionsData */
    protected $options;


    /**
     * Construct
     *
     * @param null|array|\Traversable $data
     * @param null|array|\Traversable $options
     */
    function __construct($data = null, $options = null)
    {
        if ($options !== null)
            $this->optsData()->from($options);

        parent::__construct($data);
    }


    // Options: each context may have some options describe how to represent data
    //          @see MemoryUsageContext

    /**
     * @return iOptionsData
     */
    function optsData()
    {
        if (!$this->options)
            $this->options = static::newOptsData();

        return $this->options;
    }

    /**
     * Get An Bare Options Instance
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @param null|mixed $builder Builder Options as Constructor
     *
     * @return iOptionsData
     */
    static function newOptsData($builder = null)
    {
        return (new OpenOptionsData)->from($builder);
    }
}
