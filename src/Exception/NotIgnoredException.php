<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class NotIgnoredException extends Exception
{
    const DEFAULT_CODE = 404;
    const DEFAULT_MESSAGE = 'Failed to %s \'%s\' on \'%s\': NOT_IGNORED';

    public function __construct($tubeName, Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), $tubeName, (string) $server),
            self::DEFAULT_CODE
        );
    }
}
