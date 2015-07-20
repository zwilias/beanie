<?php


namespace Beanie\Command;


use Beanie\Command;

class PeekBuriedCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_PEEK_BURIED, (new PeekBuriedCommand())->getCommandLine());
    }
}
