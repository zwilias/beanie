<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Response;

class WatchCommand extends TubeCommand
{
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
        return Command::COMMAND_WATCH;
    }

}
