<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class UnknownCommandException extends Exception
{
    const DEFAULT_CODE = 405;
    const DEFAULT_MESSAGE = 'Tried to execute unknown method \'%s\' on \'%s\'';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string)$server),
            self::DEFAULT_CODE
        );
    }
}
