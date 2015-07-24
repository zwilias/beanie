<?php


namespace Beanie\Command;



use Beanie\Exception\UnexpectedResponseException;
use Beanie\Server\Server;

class QuitCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        throw new UnexpectedResponseException($responseLine, $this, $server);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return Command::COMMAND_QUIT;
    }
}
