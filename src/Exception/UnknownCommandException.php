<?php


namespace Beanie\Exception;


class UnknownCommandException extends AbstractServerException
{
    const DEFAULT_CODE = 405;
    const DEFAULT_MESSAGE = 'Tried to execute unknown method on \'%s\'';
}
