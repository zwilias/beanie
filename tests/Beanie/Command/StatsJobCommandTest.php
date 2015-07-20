<?php


namespace Beanie\Command;

use Beanie\Command;
use Beanie\Response;
use Symfony\Component\Yaml\Yaml;

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

    public function testParseResponse_OKResponse_callsParent()
    {
        $testData = ['hello'];
        $testDataYAML = Yaml::dump($testData);
        $serverMock = $this->_getServerReturningYAMLData($testDataYAML);
        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($testDataYAML));


        $command = new StatsJobCommand(self::TEST_ID);
        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertThat($response->getData(), $this->isType('array'));
    }
}
