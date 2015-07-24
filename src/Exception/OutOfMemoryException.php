<?php


namespace Beanie\Exception;


class OutOfMemoryException extends AbstractServerCommandException
{
    const DEFAULT_CODE = 503;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': OUT_OF_MEMORY';
}
