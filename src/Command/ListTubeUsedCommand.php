<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Response;
use Beanie\Server\Server;

class ListTubeUsedCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        list(, $tubeName) = explode(' ', $responseLine, 2);
        return new Response(Response::RESPONSE_USING, $tubeName, $server);
    }

    /**
     * {@inheritdoc}
     */
    function getCommandLine()
    {
        return Command::COMMAND_LIST_TUBE_USED;
    }

}
