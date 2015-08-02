<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Command\Response;
use Beanie\Exception\BadFormatException;
use Beanie\Exception\InternalErrorException;
use Beanie\Exception\OutOfMemoryException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Exception\UnknownCommandException;
use Beanie\Server\Server;

abstract class AbstractResponseParser implements ResponseParserInterface
{
    protected static $generalErrorResponses = [
        Response::ERROR_BAD_FORMAT => BadFormatException::class,
        Response::ERROR_INTERNAL_ERROR => InternalErrorException::class,
        Response::ERROR_OUT_OF_MEMORY => OutOfMemoryException::class,
        Response::ERROR_UNKNOWN_COMMAND => UnknownCommandException::class
    ];

    /** @var string[] */
    private $acceptableResponses = [];

    /** @var array */
    private $expectedErrorResponses = [];

    /**
     * @param string[] $acceptableResponses
     * @param string[] $expectedErrorResponses
     */
    public function __construct(array $acceptableResponses = [], array $expectedErrorResponses = [])
    {
        $this->acceptableResponses = $acceptableResponses;
        $this->expectedErrorResponses = array_merge(static::$generalErrorResponses, $expectedErrorResponses);
    }

    /**
     * @param string $responseLine
     * @param Server $server
     * @return Response
     * @throws UnexpectedResponseException
     */
    abstract public function parseResponse($responseLine, Server $server);

    /**
     * @param string $responseLine
     * @return string
     */
    protected function getResponseName($responseLine)
    {
        return ($spacePosition = strpos($responseLine, ' ')) !== false
            ? substr($responseLine, 0, $spacePosition)
            : $responseLine
            ;
    }

    /**
     * @param string $responseName
     * @param Server $server
     * @throws UnexpectedResponseException
     */
    protected function ensureValidResponseName($responseName, Server $server)
    {
        if (isset($this->expectedErrorResponses[$responseName])) {
            throw new $this->expectedErrorResponses[$responseName]($server);
        }

        if (!in_array($responseName, $this->acceptableResponses)) {
            throw new UnexpectedResponseException($responseName, $server);
        }
    }
}
