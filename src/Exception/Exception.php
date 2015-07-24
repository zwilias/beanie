<?php


namespace Beanie\Exception;


class Exception extends \Exception
{
    public static function wrap(\Exception $exception, $message = null, $code = null)
    {
        return new self(
            isset($message) ? $message : $exception->getMessage(),
            isset($code) ? $code : $exception->getCode(),
            $exception
        );
    }
}
