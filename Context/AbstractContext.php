<?php
namespace Poirot\Logger\Context;

use Poirot\Core\AbstractOptions;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Core\Interfaces\iPoirotOptions;
use Poirot\Core\Interfaces\OptionsProviderInterface;
use Poirot\Core\OpenOptions;
use Poirot\Core\Traits\OpenOptionsTrait;
use Poirot\Logger\Interfaces\iContext;

abstract class AbstractContext
    implements iContext
    , OptionsProviderInterface
{
    use OpenOptionsTrait;

    /** @var string Context Name */
    protected $name;
    /** @var iPoirotOptions */
    protected $options;


    /**
     * Construct
     *
     * @param array|iDataSetConveyor $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
    }


    // ...

    /**
     * @return AbstractOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = static::optionsIns();

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
     * @return AbstractOptions
     */
    static function optionsIns()
    {
        return new OpenOptions;
    }
}