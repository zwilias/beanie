<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotIgnoredException;
use Beanie\Response;
use Beanie\Server\Server;

class IgnoreCommand extends AbstractTubeCommand
{
    /**
     * @inheritDoc
     * @throws NotIgnoredException
     */
    public function parseResponse($responseLine, Server $server)
    {
        if ($responseLine === Response::FAILURE_NOT_IGNORED) {
            throw new NotIgnoredException($this->_tubeName, $this, $server);
        }

        return parent::parseResponse($responseLine, $server);
    }

    /**
     * @inheritDoc
     */
    protected function _getExpectedResponseName()
    {
        return Response::RESPONSE_WATCHING;
    }

    /**
     * @inheritDoc
     */
    protected function _getCommandName()
    {
        return Command::COMMAND_IGNORE;
    }

}
