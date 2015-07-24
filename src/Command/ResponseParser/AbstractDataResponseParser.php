<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Command\Response;
use Beanie\Server\Server;

abstract class AbstractDataResponseParser extends AbstractResponseParser
{
    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, Server $server)
    {
        $responseName = $this->getResponseName($responseLine);
        $this->ensureValidResponseName($responseName, $server);

        $data = $this->extractData($responseLine, $server);

        return new Response($responseName, $data, $server);
    }

    /**
     * @param string $responseLine
     * @param Server $server
     * @return mixed
     */
    abstract protected function extractData($responseLine, Server $server);
}
