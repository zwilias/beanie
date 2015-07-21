<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Response;

class WatchCommand extends AbstractTubeCommand
{
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
        return Command::COMMAND_WATCH;
    }

}
