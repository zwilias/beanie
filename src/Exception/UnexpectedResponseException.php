<?php


namespace Beanie\Exception;

use Beanie\Server\Server;

class UnexpectedResponseException extends Exception
{
    const DEFAULT_CODE = 402;
    const DEFAULT_MESSAGE = 'Failed executing command on \'%s\': received unexpected response \'%s\'';

    public function __construct($response, Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, (string) $server, $response),
            self::DEFAULT_CODE
        );
    }
}
