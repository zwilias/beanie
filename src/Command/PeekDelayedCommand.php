<?php


namespace Beanie\Command;


use Beanie\Command;

class PeekDelayedCommand extends AbstractPeekCommand
{
    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return Command::COMMAND_PEEK_DELAYED;
    }

}
