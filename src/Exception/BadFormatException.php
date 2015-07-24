<?php


namespace Beanie\Exception;


class BadFormatException extends AbstractServerException
{
    const DEFAULT_CODE = 400;
    const DEFAULT_MESSAGE = 'Failed executing command on \'%s\': BAD_FORMAT';
}
