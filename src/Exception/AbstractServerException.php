<?php


namespace Beanie\Exception;


use Beanie\Server\Server;

abstract class AbstractServerException extends Exception
{
    const DEFAULT_MESSAGE = 'Error on server \'%s\'';
    const DEFAULT_CODE = 666;

    public function __construct(Server $server)
    {
        parent::__construct(
            sprintf(static::DEFAULT_MESSAGE, (string) $server),
            static::DEFAULT_CODE
        );
    }
}
