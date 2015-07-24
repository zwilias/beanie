<?php


namespace Beanie\Command;



use Beanie\Exception\NotIgnoredException;

use Beanie\Server\Server;

class IgnoreCommand extends AbstractTubeCommand
{
    /**
     * @inheritDoc
     * @throws NotIgnoredException
     */
    public function parseResponseLine($responseLine, Server $server)
    {
        if ($responseLine === Response::FAILURE_NOT_IGNORED) {
            throw new NotIgnoredException($this->tubeName, $this, $server);
        }

        return parent::parseResponseLine($responseLine, $server);
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedResponseName()
    {
        return Response::RESPONSE_WATCHING;
    }

    /**
     * @inheritDoc
     */
    protected function getCommandName()
    {
        return Command::COMMAND_IGNORE;
    }

}
