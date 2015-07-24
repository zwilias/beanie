<?php


namespace Beanie\Command;




class StatsCommand extends AbstractWithYAMLResponseCommand
{
    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return Command::COMMAND_STATS;
    }
}
