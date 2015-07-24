<?php


namespace Beanie\Exception;


class DrainingException extends AbstractServerException
{
    const DEFAULT_CODE = 409;
    const DEFAULT_MESSAGE = 'Server \'%s\' is draining';
}
