<?php


namespace Beanie\Exception;


class DeadlineSoonException extends AbstractServerException
{
    const DEFAULT_CODE = 408;
    const DEFAULT_MESSAGE = 'There is a deadline for a running job approaching soon on \'%s\'.';
}
