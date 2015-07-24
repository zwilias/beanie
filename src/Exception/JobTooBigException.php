<?php


namespace Beanie\Exception;


class JobTooBigException extends AbstractServerException
{
    const DEFAULT_CODE = 413;
    const DEFAULT_MESSAGE = 'The job was too big to handle for \'%s\'';
}
