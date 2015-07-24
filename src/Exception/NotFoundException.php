<?php


namespace Beanie\Exception;


class NotFoundException extends AbstractServerCommandException
{
    const DEFAULT_CODE = 404;
    const DEFAULT_MESSAGE = 'Failed executing \'%s\' command on \'%s\': NOT_FOUND';
}
