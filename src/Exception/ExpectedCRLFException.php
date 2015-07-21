<?php


namespace Beanie\Exception;


use Beanie\Exception;
use Beanie\Server\Server;

class ExpectedCRLFException extends Exception
{
    const DEFAULT_CODE = 400;
    const DEFAULT_MESSAGE = 'The command was misformed, expected CRLF on \'%s\'';

    public function __construct(Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, (string) $server),
            self::DEFAULT_CODE
        );
    }
}
