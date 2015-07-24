<?php


namespace Beanie\Exception;


use Beanie\Command\Command;

use Beanie\Server\Server;

class UnexpectedResponseException extends Exception
{
    const DEFAULT_CODE = 402;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': received unexpected response \'%s\'';

    public function __construct($response, Command $command, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, get_class($command), (string) $server, $response),
            self::DEFAULT_CODE
        );
    }
}
