<?php


namespace Beanie\Exception;


use Beanie\Exception;

class InvalidNameException extends Exception
{
    const DEFAULT_CODE = 400;

    public function __construct($message = '', $code = self::DEFAULT_CODE, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
