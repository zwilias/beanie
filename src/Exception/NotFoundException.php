<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class NotFoundException extends Exception
{
    const DEFAULT_CODE = 404;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': NOT_FOUND';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string)$server),
            self::DEFAULT_CODE
        );
    }
}
