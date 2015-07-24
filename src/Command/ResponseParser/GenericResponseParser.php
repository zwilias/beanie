<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Command\Response;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Server\Server;

class GenericResponseParser extends AbstractResponseParser
{
    /**
     * @param string $responseLine
     * @param Server $server
     * @return Response
     * @throws UnexpectedResponseException
     */
    public function parseResponse($responseLine, Server $server)
    {
        $responseName = $this->getResponseName($responseLine);
        $this->ensureValidResponseName($responseName, $server);

        return new Response($responseName, null, $server);
    }
}
