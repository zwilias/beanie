<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class OutOfMemoryException extends Exception
{
    const DEFAULT_CODE = 503;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': OUT_OF_MEMORY';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string) $server),
            self::DEFAULT_CODE
        );
    }
}
