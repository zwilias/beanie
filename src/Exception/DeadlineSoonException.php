<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Server\Server;

class DeadlineSoonException extends Exception
{
    const DEFAULT_CODE = 408;
    const DEFAULT_MESSAGE = 'There is a deadline for a running job approaching soon on \'%s\'. \'%s\' failed.';

    public function __construct(Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, (string)$server, get_class($command)),
            self::DEFAULT_CODE
        );
    }
}
