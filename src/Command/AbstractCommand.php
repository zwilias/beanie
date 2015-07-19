<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\BadFormatException;
use Beanie\Exception\InternalErrorException;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\OutOfMemoryException;
use Beanie\Response;
use Beanie\ResponseParser;
use Beanie\Server\Server;

abstract class AbstractCommand implements Command, ResponseParser
{
    /**
     * {@inheritdoc}
     * @throws BadFormatException
     * @throws InternalErrorException
     * @throws NotFoundException
     * @throws OutOfMemoryException
     */
    public function parseResponse($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::ERROR_BAD_FORMAT:
                throw new BadFormatException($this, $server);
            case Response::ERROR_INTERNAL_ERROR:
                throw new InternalErrorException($this, $server);
            case Response::ERROR_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::ERROR_OUT_OF_MEMORY:
                throw new OutOfMemoryException($this, $server);
            default:
                return $this->_parseResponse($responseLine, $server);
        }
    }

    /**
     * @param string $responseLine
     * @param Server $server
     * @return Response
     */
    protected abstract function _parseResponse($responseLine, Server $server);
}
