<?php


namespace Beanie\Exception;


class OutOfMemoryException extends AbstractServerException
{
    const DEFAULT_CODE = 503;
    const DEFAULT_MESSAGE = 'Failed executing command on \'%s\': OUT_OF_MEMORY';
}
