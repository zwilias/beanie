<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception;
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

    /**
     * @param string $name
     * @throws Exception\InvalidNameException
     */
    protected function _ensureValidName($name)
    {
        if (!(
            is_string($name) &&
            strlen($name) <= 200 &&
            preg_match(Command::VALID_NAME_REGEX, $name)
        )) {
            if (is_object($name) && !method_exists($name, '__toString')) {
                $name = sprintf('{object of type %s', get_class($name));
            }

            throw new Exception\InvalidNameException("Invalid name: {$name}", 400);
        }
    }

    public function hasData()
    {
        return $this->getData() !== null;
    }

    public function getData()
    {
        return null;
    }
}
