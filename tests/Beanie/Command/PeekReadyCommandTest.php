<?php


namespace Beanie\Command;



class PeekReadyCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_PEEK_READY, (new PeekReadyCommand())->getCommandLine());
    }
}
