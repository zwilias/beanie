<?php


namespace Beanie\Exception;


class InternalErrorException extends AbstractServerException
{
    const DEFAULT_CODE = 500;
    const DEFAULT_MESSAGE = 'Failed executing command on \'%s\': INTERNAL ERROR';
}
