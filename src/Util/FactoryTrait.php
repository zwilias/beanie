<?php


namespace Beanie\Util;


trait FactoryTrait
{
    /** @var static */
    private static $instance;

    /**
     * @return static
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function unsetInstance()
    {
        self::$instance = null;
    }
}
