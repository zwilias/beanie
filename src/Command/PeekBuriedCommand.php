<?php


namespace Beanie\Command;




class PeekBuriedCommand extends AbstractPeekCommand
{
    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return Command::COMMAND_PEEK_BURIED;
    }
}
