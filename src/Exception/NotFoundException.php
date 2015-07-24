<?php


namespace Beanie\Exception;


class NotFoundException extends AbstractServerException
{
    const DEFAULT_CODE = 404;
    const DEFAULT_MESSAGE = 'Failed executing command on \'%s\': NOT_FOUND';
}
