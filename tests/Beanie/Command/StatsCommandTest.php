<?php


namespace Beanie\Command;


use Beanie\Command;

class StatsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_STATS, (new StatsCommand())->getCommandLine());
    }
}
