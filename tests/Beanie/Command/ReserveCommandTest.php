<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class ReserveCommandTest extends WithServerMock_TestCase
{
    public function testConstruct_reserveMode()
    {
        $command = new ReserveCommand(ReserveCommand::MODE_RESERVE);


        $this->assertInstanceOf('\Beanie\Command\ReserveCommand', $command);
    }

    public function testConstruct_reserveWithTimeoutMode()
    {
        $command = new ReserveCommand(ReserveCommand::MODE_RESERVE_WITH_TIMEOUT);


        $this->assertInstanceOf('\Beanie\Command\ReserveCommand', $command);
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testConstruct_unknownMode_throwsException()
    {
        new ReserveCommand('whatever');
    }

    public function testGetCommandLine_noArgs_usesDefaults()
    {
        $expected = Command::COMMAND_RESERVE;


        $this->assertEquals($expected, (new ReserveCommand())->getCommandLine());
    }

    public function testGetCommandLine_reserveNoTimeout_withTimeout_timeoutIsIgnored()
    {
        $expected = Command::COMMAND_RESERVE;


        $this->assertEquals($expected, (new ReserveCommand(ReserveCommand::MODE_RESERVE, 20))->getCommandLine());
    }

    public function testGetCommandLine_reserveWithTimeout_defaultTimeout()
    {
        $expected = sprintf('%s %s', Command::COMMAND_RESERVE_WITH_TIMEOUT, 0);


        $this->assertEquals($expected, (new ReserveCommand(ReserveCommand::MODE_RESERVE_WITH_TIMEOUT))->getCommandLine());
    }

    public function testGetCommandLine_reserveWithTimeout_respectsTimeout()
    {
        $timeout = 10;
        $expected = sprintf('%s %s', Command::COMMAND_RESERVE_WITH_TIMEOUT, $timeout);


        $this->assertEquals(
            $expected,
            (new ReserveCommand(ReserveCommand::COMMAND_RESERVE_WITH_TIMEOUT, $timeout))->getCommandLine()
        );
    }

    /**
     * @expectedException \Beanie\Exception\DeadlineSoonException
     * @expectedExceptionCode 408
     */
    public function testParseResponse_deadlineSoonFailure_throwsDeadlineSoonException()
    {
        (new ReserveCommand())->parseResponse(Response::FAILURE_DEADLINE_SOON, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\TimedOutException
     * @expectedExceptionCode 504
     */
    public function testParseResponse_timedOutFailure_throwsTimeOutFailureException()
    {
        (new ReserveCommand())->parseResponse(Response::FAILURE_TIMED_OUT, $this->_getServerMock());
    }

    public function testParseResponse_readsDataFromServer_returnsResponse()
    {
        $data = "dit is wat data";
        $jobId = 123;
        $responseLine = join(' ', [
            Response::RESPONSE_RESERVED,
            $jobId,
            strlen($data)
        ]);

        $serverMock = $this->_getServerMock();

        $serverMock->expects($this->once())
            ->method('readData')
            ->with(strlen($data))
            ->willReturn($data);

        $command = new ReserveCommand();


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_RESERVED, $response->getName());
        $this->assertEquals([
            'id' => $jobId,
            'data' => $data
        ], $response->getData());
        $this->assertEquals($serverMock, $response->getServer());
    }
}
