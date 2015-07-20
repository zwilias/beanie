<?php


namespace Beanie\Command;


use Beanie\Command;

class ListTubesCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandLine_correctFormat()
    {
        $this->assertEquals(Command::COMMAND_LIST_TUBES, (new ListTubesCommand())->getCommandLine());
    }
}
