<?php


namespace Beanie\Exception;


class ExpectedCRLFException extends AbstractServerException
{
    const DEFAULT_CODE = 400;
    const DEFAULT_MESSAGE = 'The command was misformed, expected CRLF on \'%s\'';
}
