<?php


namespace Beanie\Command;


use Beanie\Command;

class PeekReadyCommand extends AbstractPeekCommand
{
    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return Command::COMMAND_PEEK_READY;
    }
}
