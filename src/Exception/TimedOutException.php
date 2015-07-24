<?php


namespace Beanie\Exception;


class TimedOutException extends AbstractServerException
{
    const DEFAULT_CODE = 504;
    const DEFAULT_MESSAGE = 'Could not get response from \'%s\' within the specified timeout';
}
