<?php


namespace Beanie\Command;


use Beanie\Command;

class ListTubesCommand extends AbstractWithYAMLResponseCommand
{
    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return Command::COMMAND_LIST_TUBES;
    }
}
