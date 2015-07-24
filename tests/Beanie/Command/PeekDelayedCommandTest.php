<?php


namespace Beanie\Command;




class PeekDelayedCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_PEEK_DELAYED, (new PeekDelayedCommand())->getCommandLine());
    }
}
