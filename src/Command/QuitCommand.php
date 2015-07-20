<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Server\Server;

class QuitCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        throw new UnexpectedResponseException($responseLine, $this, $server);
    }

    /**
     * {@inheritdoc}
     */
    function getCommandLine()
    {
        return Command::COMMAND_QUIT;
    }
}