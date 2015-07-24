<?php


namespace Beanie\Command;


class UseCommand extends AbstractTubeCommand
{
    /**
     * @inheritDoc
     */
    protected function getExpectedResponseName()
    {
        return Response::RESPONSE_USING;
    }

    /**
     * @inheritDoc
     */
    protected function getCommandName()
    {
        return Command::COMMAND_USE;
    }
}
