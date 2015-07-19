<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Response;

class UseCommand extends TubeCommand
{
    /**
     * @inheritDoc
     */
    protected function _getExpectedResponseName()
    {
        return Response::RESPONSE_USING;
    }

    /**
     * @inheritDoc
     */
    protected function _getCommandName()
    {
        return Command::COMMAND_USE;
    }
}
