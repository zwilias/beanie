<?php


namespace Beanie\Command;


use Beanie\Command;

class ListTubesWatchedCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_LIST_TUBES_WATCHED, (new ListTubesWatchedCommand())->getCommandLine());
    }
}
