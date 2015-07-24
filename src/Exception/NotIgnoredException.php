<?php


namespace Beanie\Exception;


class NotIgnoredException extends AbstractServerException
{
    const DEFAULT_CODE = 404;
    const DEFAULT_MESSAGE = 'Couldn\'t ignore tube on \'%s\': NOT_IGNORED';
}
