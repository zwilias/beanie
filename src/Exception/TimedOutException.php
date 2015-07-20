<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class TimedOutException extends Exception
{
    const DEFAULT_CODE = 504;
    const DEFAULT_MESSAGE = 'Failed \'%s\': could not get response from \'%s\' within the specified timeout';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string)$server),
            self::DEFAULT_CODE
        );
    }
}
