<?php


namespace Beanie\Command;


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
