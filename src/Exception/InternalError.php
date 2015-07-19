<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class InternalError extends Exception
{
    const DEFAULT_CODE = 500;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': INTERNAL ERROR';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string)$server),
            self::DEFAULT_CODE
        );
    }
}
