<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

abstract class AbstractServerCommandException extends Exception
{
    const DEFAULT_MESSAGE = 'Command: %s, Server: %s';
    const DEFAULT_CODE = 666;

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(static::DEFAULT_MESSAGE, get_class($command), (string) $server),
            static::DEFAULT_CODE
        );
    }
}
