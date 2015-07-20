<?php


namespace Beanie\Command;

use Beanie\Command;

class PeekCommandTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ID = 567;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_PEEK, self::TEST_ID);


        $this->assertEquals($expected, (new PeekCommand(self::TEST_ID))->getCommandLine());
    }
}
