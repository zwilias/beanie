<?php


namespace Beanie\Exception;



use Beanie\Server\Server;

class JobTooBigException extends Exception
{
    const DEFAULT_CODE = 413;
    const DEFAULT_MESSAGE = 'The job was too big to handle for \'%s\'';

    public function __construct(Server $server)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, (string) $server),
            self::DEFAULT_CODE
        );
    }
}
