<?php


namespace Beanie\Command;




class ListTubesWatchedCommand extends AbstractWithYAMLResponseCommand
{
    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return Command::COMMAND_LIST_TUBES_WATCHED;
    }
}
