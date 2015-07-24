<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Command\Response;
use Beanie\Server\Server;

interface ResponseParser
{
    /**
     * @param string $responseLine
     * @param Server $server
     * @return \Beanie\Command\Response
     */
    function parseResponse($responseLine, Server $server);
}
