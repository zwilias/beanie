<?php


namespace Beanie\Util;


interface Factory
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
