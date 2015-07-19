<?php


namespace Beanie\Exception;


use Beanie\Exception;
use Beanie\Server\Server;

class DrainingException extends Exception
{
    const DEFAULT_CODE = 409;
    const DEFAULT_MESSAGE = 'Server \'%s\' is draining';

    public function __construct(Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, (string)$server),
            self::DEFAULT_CODE
        );
    }
}
