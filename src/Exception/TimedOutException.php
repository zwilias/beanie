<?php


namespace Beanie\Exception;


class TimedOutException extends AbstractServerCommandException
{
    const DEFAULT_CODE = 504;
    const DEFAULT_MESSAGE = 'Failed \'%s\': could not get response from \'%s\' within the specified timeout';
}
