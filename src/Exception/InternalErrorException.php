<?php


namespace Beanie\Exception;


use Beanie\Command;
use Beanie\Exception;


class InternalErrorException extends AbstractServerCommandException
{
    const DEFAULT_CODE = 500;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': INTERNAL ERROR';
}
