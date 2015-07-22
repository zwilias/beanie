<?php


namespace Beanie;


use Beanie\Server\Server;

interface ResponseParser
{
    /**
     * @param string $responseLine
     * @param Server $server
     * @return Response
     */
    function parseResponse($responseLine, Server $server);
}
