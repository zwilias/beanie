<?php


namespace Beanie\Command;

use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class StatsJobCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 98123;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_STATS_JOB, self::TEST_ID);


        $command = new StatsJobCommand(self::TEST_ID);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new StatsJobCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }
}
