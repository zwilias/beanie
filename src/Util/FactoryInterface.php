<?php


namespace Beanie\Util;


interface FactoryInterface
{
    /**
     * @return static
     */
    public static function instance();

    /**
     * @return void
     */
    public static function unsetInstance();
}
