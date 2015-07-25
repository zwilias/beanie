<?php


require_once __DIR__ . '/Beanie/Command/WithServerMock_TestCase.php';

use Beanie\Beanie;
use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Command\Response;
use Beanie\Command\WithServerMock_TestCase;
use Beanie\Exception\DrainingException;
use Beanie\Exception\ExpectedCRLFException;
use Beanie\Exception\JobTooBigException;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversNothing
 */
class AllCommandsTest extends WithServerMock_TestCase
{
    const TEST_ID = 999;

    /**
     * @var CommandFactory
     */
    private $commandFactory;

    public function setUp()
    {
        $this->commandFactory = CommandFactory::instance();
    }

    // BURY COMMAND //

    public function testBuryCommand_GetCommandLine_noArgs_usesDefault()
    {
        $buryCommand = $this->commandFactory->create(Command::COMMAND_BURY, [self::TEST_ID]);
        $expected = join(' ', [
            Command::COMMAND_BURY,
            self::TEST_ID,
            Beanie::DEFAULT_PRIORITY
        ]);


        $this->assertEquals($expected, $buryCommand->getCommandLine());
    }

    public function testBuryCommand_GetCommandLine_withArgs_usesArgs()
    {
        $priority = 8888;
        $expected = join(' ', [
            Command::COMMAND_BURY,
            self::TEST_ID,
            $priority
        ]);


        $buryCommand = $this->commandFactory->create(Command::COMMAND_BURY, [self::TEST_ID, $priority]);


        $this->assertEquals($expected, $buryCommand->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testBuryCommand_ParseResponse_notFoundFailure_throwsException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_BURY, [self::TEST_ID])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedException 402
     */
    public function testBuryCommand_ParseResponse_unexpectedResponse_throwsException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_BURY, [self::TEST_ID])
            ->parseResponse('WTF', $this->_getServerMock());
    }

    public function testBuryCommand_ParseResponse_buriedResponse_returnsResponse()
    {
        $response = $this->commandFactory
            ->create(Command::COMMAND_BURY, [self::TEST_ID])
            ->parseResponse(Response::RESPONSE_BURIED, $this->_getServerMock());


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::RESPONSE_BURIED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // DELETE COMMAND //

    public function testDeleteCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_DELETE, self::TEST_ID);


        $deleteCommand = $this->commandFactory->create(Command::COMMAND_DELETE, [self::TEST_ID]);


        $this->assertEquals($expected, $deleteCommand->getCommandLine());
    }

    public function testDeleteCommand_ParseResponse_deleted_returnsResponseWithoutData()
    {
        $deleteCommand = $this->commandFactory->create(Command::COMMAND_DELETE, [self::TEST_ID]);


        $response = $deleteCommand->parseResponse(Response::RESPONSE_DELETED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_DELETED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testDeleteCommand_ParseResponse_unexpectedResponse_throwsException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_DELETE, [self::TEST_ID])
            ->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testDeleteCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_DELETE, [self::TEST_ID])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    // IGNORE COMMAND //

    public function testIgnoreCommand_GetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_IGNORE, $tubeName);


        $commandLine = $this->commandFactory
            ->create(Command::COMMAND_IGNORE, [$tubeName])
            ->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testIgnoreCommand_ParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_WATCHING, Beanie::DEFAULT_TUBE);
        $ignoreCommand = $this->commandFactory->create(Command::COMMAND_IGNORE, [Beanie::DEFAULT_TUBE]);


        $response = $ignoreCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_WATCHING, $response->getName());
        $this->assertEquals(Beanie::DEFAULT_TUBE, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\NotIgnoredException
     * @expectedExceptionCode 404
     */
    public function testIgnoreCommand_ParseResponse_notIgnore_throwsNotIgnoreException()
    {
        $ignoreCommand = $this->commandFactory->create(Command::COMMAND_IGNORE, [Beanie::DEFAULT_TUBE]);


        $ignoreCommand->parseResponse(Response::FAILURE_NOT_IGNORED, $this->_getServerMock());
    }

    // KICK COMMAND //
    public function testKickCommand_GetCommandLine_noArgs_usesDefaults()
    {
        $expected = sprintf('%s %s', Command::COMMAND_KICK, Beanie::DEFAULT_MAX_TO_KICK);


        $command = $this->commandFactory->create(Command::COMMAND_KICK);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testKickCommand_GetCommandLine_withArgs_usesArgs()
    {
        $maxToKick = 10;
        $expected = sprintf('%s %s', Command::COMMAND_KICK, $maxToKick);


        $command = $this->commandFactory->create(Command::COMMAND_KICK, [$maxToKick]);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testKickCommand_ParseResponse_kicked_returnsResponse()
    {
        $kicked = 10;
        $responseLine = sprintf('%s %s', Response::RESPONSE_KICKED, $kicked);


        $command = $this->commandFactory->create(Command::COMMAND_KICK);
        $response = $command->parseResponse($responseLine, $this->_getServerMock());


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::RESPONSE_KICKED, $response->getName());
        $this->assertEquals($kicked, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // KICK-JOB COMMAND //
    public function testKickJobCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_KICK_JOB, self::TEST_ID);


        $kickCommand = $this->commandFactory->create(Command::COMMAND_KICK_JOB, [self::TEST_ID]);


        $this->assertEquals($expected, $kickCommand->getCommandLine());
    }

    public function testKickJobCommand_ParseResponse_deleted_returnsResponseWithoutData()
    {
        $kickCommand = $this->commandFactory->create(Command::COMMAND_KICK_JOB, [self::TEST_ID]);


        $response = $kickCommand->parseResponse(Response::RESPONSE_KICKED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_KICKED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testKickJobCommand_ParseResponse_unexpectedResponse_throwsException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_KICK_JOB, [self::TEST_ID])
            ->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testKickJobCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_KICK_JOB, [self::TEST_ID])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    // LIST-TUBES COMMAND //
    public function testListTubesCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_LIST_TUBES,
            $this->commandFactory->create(Command::COMMAND_LIST_TUBES)->getCommandLine()
        );
    }

    // LIST-TUBES-WATCHED COMMAND //
    public function testListTubesWatchedCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_LIST_TUBES_WATCHED,
            $this->commandFactory->create(Command::COMMAND_LIST_TUBES_WATCHED)->getCommandLine()
        );
    }

    // LIST-TUBE-USED COMMAND //
    public function testListTubeUsedCommand_GetCommandLine_correctCommandLine()
    {
        $this->assertEquals(
            Command::COMMAND_LIST_TUBE_USED,
            $this->commandFactory->create(Command::COMMAND_LIST_TUBE_USED)->getCommandLine()
        );
    }

    public function testListTubeUsedCommand_ParseResponse_returnsResponse_withTubeUsed()
    {
        $tubeUsed = 'some-tube-name';
        $responseLine = sprintf('%s %s', Response::RESPONSE_USING, $tubeUsed);


        $command = $this->commandFactory->create(Command::COMMAND_LIST_TUBE_USED);
        $response = $command->parseResponse($responseLine, $this->_getServerMock());


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::RESPONSE_USING, $response->getName());
        $this->assertEquals($tubeUsed, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // PAUSE-TUBE COMMAND //
    const TEST_TUBE = 'tube';

    /**
     * @param mixed $tubeName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testPauseTubeCommand_Construct_invalidTubeName_ThrowsInvalidNameException($tubeName)
    {
        $this->commandFactory->create(Command::COMMAND_PAUSE_TUBE, [$tubeName]);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testPauseTubeCommand_Construct_validTubeName_noException($tubeName)
    {
        $command = $this->commandFactory->create(Command::COMMAND_PAUSE_TUBE, [$tubeName]);


        $this->assertInstanceOf(Command::class, $command);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testPauseTubeCommand_GetCommandLine_noArgs_usesDefaults($tubeName)
    {
        $expected = join(' ', [Command::COMMAND_PAUSE_TUBE, $tubeName, Beanie::DEFAULT_DELAY]);
        $command = $this->commandFactory->create(Command::COMMAND_PAUSE_TUBE, [$tubeName]);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testPauseTubeCommand_GetCommandLine_args_usesArgs()
    {
        $delay = 123;
        $expected = join(' ', [Command::COMMAND_PAUSE_TUBE, self::TEST_TUBE, $delay]);


        $command = $this->commandFactory->create(Command::COMMAND_PAUSE_TUBE, [self::TEST_TUBE, $delay]);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testPauseTubeCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_PAUSE_TUBE, [self::TEST_TUBE])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testPauseTubeCommand_ParseResponse_unexpectedResponse_throwsUnexpectedResponseException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_PAUSE_TUBE, [self::TEST_TUBE])
            ->parseResponse('something', $this->_getServerMock());
    }

    public function testPauseTubeCommand_ParseResponse_pausedResponse_returnsResponse()
    {
        $response = $this->commandFactory
            ->create(Command::COMMAND_PAUSE_TUBE, [self::TEST_TUBE])
            ->parseResponse(Response::RESPONSE_PAUSED, $this->_getServerMock());


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::RESPONSE_PAUSED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // PEEK COMMAND //

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testPeekCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\Command $command */
        $command = $this->commandFactory->create(Command::COMMAND_PEEK, [self::TEST_TUBE]);
        $command->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    public function testPeekCommand_ParseResponse_foundResponse_returnsResponse()
    {
        $data = 'this is some testdata and might as well be an actual job';

        $serverMock = $this->_getServerMock();
        $serverMock->expects($this->once())
            ->method('readData')
            ->with(strlen($data))
            ->willReturn($data);

        $responseLine = join(' ', [
            Response::RESPONSE_FOUND,
            self::TEST_ID,
            strlen($data)
        ]);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\Command $command */
        $command = $this->commandFactory->create(Command::COMMAND_PEEK, [self::TEST_ID]);


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::RESPONSE_FOUND, $response->getName());
        $this->assertEquals($serverMock, $response->getServer());
        $this->assertEquals([
            'id' => self::TEST_ID,
            'data' => $data
        ], $response->getData());
    }

    public function testPeekCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_PEEK, self::TEST_ID);


        $this->assertEquals(
            $expected,
            $this->commandFactory->create(Command::COMMAND_PEEK, [self::TEST_ID])->getCommandLine()
        );
    }

    // PEEK BURIED //
    public function testPeekBuriedCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_PEEK_BURIED,
            $this->commandFactory->create(Command::COMMAND_PEEK_BURIED)->getCommandLine()
        );
    }

    // PEEK DELAYED //
    public function testPeekDelayedCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_PEEK_DELAYED,
            $this->commandFactory->create(Command::COMMAND_PEEK_DELAYED)->getCommandLine()
        );
    }

    // PEEK READY //
    public function testPeekReadyCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_PEEK_READY,
            $this->commandFactory->create(Command::COMMAND_PEEK_READY)->getCommandLine()
        );
    }

    // PUT COMMAND //
    const TEST_DATA = 'testdata';

    public function testPutCommand_Construct_withData_containsData()
    {
        $putCommand = $this->commandFactory->create(Command::COMMAND_PUT, [self::TEST_DATA]);


        $this->assertTrue($putCommand->hasData());
        $this->assertEquals(self::TEST_DATA, $putCommand->getData());
    }

    public function testPutCommand_GetCommandLine_noArgs_defaultValues()
    {
        $expected = join(' ', [
            Command::COMMAND_PUT,
            Beanie::DEFAULT_PRIORITY,
            Beanie::DEFAULT_DELAY,
            Beanie::DEFAULT_TIME_TO_RUN,
            strlen(self::TEST_DATA)
        ]);


        $putCommand = $this->commandFactory->create(Command::COMMAND_PUT, [self::TEST_DATA]);


        $this->assertEquals($expected, $putCommand->getCommandLine());
    }

    public function testPutCommand_GetCommandLine_args_usesArgs()
    {
        $priority = 5;
        $delay = 6;
        $timeToRun = 7;

        $expected = join(' ', [
            Command::COMMAND_PUT,
            $priority,
            $delay,
            $timeToRun,
            strlen(self::TEST_DATA)
        ]);


        $putCommand = $this->commandFactory
            ->create(Command::COMMAND_PUT, [self::TEST_DATA, $priority, $delay, $timeToRun]);


        $this->assertEquals($expected, $putCommand->getCommandLine());
    }

    /**
     * @param $response
     * @param $exceptionClass
     * @param $exceptionCode
     *
     * @dataProvider putCommand_failureResponses
     */
    public function testPutCommand_ParseResponse_responseFailure_throwsAppropriateException
    (
        $response,
        $exceptionClass,
        $exceptionCode
    )
    {
        $putCommand = $this->commandFactory->create(Command::COMMAND_PUT, [self::TEST_DATA]);
        $caughtException = false;

        try {
            $putCommand->parseResponse($response, $this->_getServerMock());
        } catch (\Exception $exception) {
            $caughtException = true;

            $this->assertInstanceOf($exceptionClass, $exception);
            $this->assertEquals($exceptionCode, $exception->getCode());
        }

        if (!$caughtException) {
            $this->fail('Expected exception of type ' . $exceptionClass);
        }
    }

    /**
     * @param $response
     * @param $type
     * @param $data
     *
     * @dataProvider putCommand_successResponses
     */
    public function testPutCommand_ParseResponse_responseSuccess($response, $type, $data)
    {
        $putCommand = $this->commandFactory->create(Command::COMMAND_PUT, [self::TEST_DATA]);


        $response = $putCommand->parseResponse($response, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals($type, $response->getName());
        $this->assertEquals($data, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // QUIT COMMAND //
    public function testQuitCommand_GetCommandLine_correctCommand()
    {
        $this->assertEquals(
            Command::COMMAND_QUIT,
            $this->commandFactory->create(Command::COMMAND_QUIT)->getCommandLine()
        );
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testQuitCommand_ParseResponse_noResponseExpected_throwsUnexpectedResponse()
    {
        $this->commandFactory
            ->create(Command::COMMAND_QUIT)
            ->parseResponse('something', $this->_getServerMock());
    }

    // RELEASE COMMAND //
    public function testReleaseCommand_GetCommandLine_noArgs_defaultValues()
    {
        $expected = join(' ', [
            Command::COMMAND_RELEASE,
            self::TEST_ID,
            Beanie::DEFAULT_PRIORITY,
            Beanie::DEFAULT_DELAY
        ]);


        $releaseCommand = $this->commandFactory->create(Command::COMMAND_RELEASE, [self::TEST_ID]);


        $this->assertEquals($expected, $releaseCommand->getCommandLine());
    }

    public function testReleaseCommand_GetCommandLine_args_usesArgs()
    {
        $priority = 5;
        $delay = 6;

        $expected = join(' ', [
            Command::COMMAND_RELEASE,
            self::TEST_ID,
            $priority,
            $delay
        ]);


        $releaseCommand =$this->commandFactory
            ->create(Command::COMMAND_RELEASE, [self::TEST_ID, $priority, $delay]);


        $this->assertEquals($expected, $releaseCommand->getCommandLine());
    }

    /**
     * @param $response
     * @param $exceptionClass
     * @param $exceptionCode
     *
     * @dataProvider failureReleaseCommand_Responses
     */
    public function testReleaseCommand_ParseResponse_responseFailure_throwsAppropriateException
    (
        $response,
        $exceptionClass,
        $exceptionCode
    )
    {
        $releaseCommand = $this->commandFactory->create(Command::COMMAND_RELEASE, [self::TEST_ID]);
        $caughtException = false;

        try {
            $releaseCommand->parseResponse($response, $this->_getServerMock());
        } catch (\Exception $exception) {
            $caughtException = true;

            $this->assertInstanceOf($exceptionClass, $exception);
            $this->assertEquals($exceptionCode, $exception->getCode());
        }

        if (!$caughtException) {
            $this->fail('Expected exception of type ' . $exceptionClass);
        }
    }

    /**
     * @param $responseName
     *
     * @dataProvider successReleaseCommand_Responses
     */
    public function testReleaseCommand_ParseResponse_responseSuccess($responseName)
    {
        $releaseCommand = $this->commandFactory->create(Command::COMMAND_RELEASE, [self::TEST_ID]);


        $response = $releaseCommand->parseResponse($responseName, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals($responseName, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    public function failureReleaseCommand_Responses()
    {
        return [
            'not found' => [
                Response::FAILURE_NOT_FOUND,
                NotFoundException::class,
                NotFoundException::DEFAULT_CODE
            ],
            'unexpected' => [
                'WHAT',
                UnexpectedResponseException::class,
                UnexpectedResponseException::DEFAULT_CODE
            ]
        ];
    }

    public function successReleaseCommand_Responses()
    {
        return [
            'release' => [
                Response::RESPONSE_RELEASED
            ],
            'buried' => [
                Response::RESPONSE_BURIED
            ]
        ];
    }

    // RESERVE COMMAND //
    public function testReserveCommand_GetCommandLine_noArgs_usesDefaults()
    {
        $expected = Command::COMMAND_RESERVE;


        $this->assertEquals(
            $expected,
            $this->commandFactory->create(Command::COMMAND_RESERVE)->getCommandLine()
        );
    }

    /**
     * @expectedException \Beanie\Exception\DeadlineSoonException
     * @expectedExceptionCode 408
     */
    public function testReserveCommand_ParseResponse_deadlineSoonFailure_throwsDeadlineSoonException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_RESERVE)
            ->parseResponse(Response::FAILURE_DEADLINE_SOON, $this->_getServerMock());
    }

    public function testReserveCommand_ParseResponse_readsDataFromServer_returnsResponse()
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

        $command = $this->commandFactory->create(Command::COMMAND_RESERVE);


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_RESERVED, $response->getName());
        $this->assertEquals([
            'id' => $jobId,
            'data' => $data
        ], $response->getData());
        $this->assertEquals($serverMock, $response->getServer());
    }

    // RESERVE-WITH-TIMEOUT COMMAND //
    const TEST_TIMEOUT = 50;
    public function testReserveCommandWithTimeout_GetCommandLine_noArgs_usesDefaults()
    {
        $expected = sprintf('%s %s', Command::COMMAND_RESERVE_WITH_TIMEOUT, self::TEST_TIMEOUT);


        $this->assertEquals(
            $expected,
            $this->commandFactory
                ->create(Command::COMMAND_RESERVE_WITH_TIMEOUT, [self::TEST_TIMEOUT])
                ->getCommandLine()
        );
    }

    /**
     * @expectedException \Beanie\Exception\DeadlineSoonException
     * @expectedExceptionCode 408
     */
    public function testReserveCommandWithTimeout_ParseResponse_deadlineSoonFailure_throwsDeadlineSoonException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_RESERVE_WITH_TIMEOUT, [self::TEST_TIMEOUT])
            ->parseResponse(Response::FAILURE_DEADLINE_SOON, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\TimedOutException
     * @expectedExceptionCode 504
     */
    public function testReserveCommandWithTimeout_ParseResponse_timedOutFailure_throwsTimedOutException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_RESERVE_WITH_TIMEOUT, [self::TEST_TIMEOUT])
            ->parseResponse(Response::FAILURE_TIMED_OUT, $this->_getServerMock());
    }

    public function testReserveCommandWithTimeout_ParseResponse_readsDataFromServer_returnsResponse()
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

        $command = $this->commandFactory->create(Command::COMMAND_RESERVE_WITH_TIMEOUT, [self::TEST_TIMEOUT]);


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_RESERVED, $response->getName());
        $this->assertEquals([
            'id' => $jobId,
            'data' => $data
        ], $response->getData());
        $this->assertEquals($serverMock, $response->getServer());
    }

    // STATS COMMAND //
    public function testStatsCommand_GetCommandLine_correctFormat()
    {
        $this->assertEquals(
            Command::COMMAND_STATS,
            $this->commandFactory->create(Command::COMMAND_STATS)->getCommandLine()
        );
    }

    // STATS-JOB COMMAND //

    public function testStatsJobCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_STATS_JOB, self::TEST_ID);


        $command = $this->commandFactory->create(Command::COMMAND_STATS_JOB, [self::TEST_ID]);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testStatsJobCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_STATS_JOB, [self::TEST_ID])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    public function testStatsJobCommand_ParseResponse_OKResponse_callsParent()
    {
        $testData = ['hello'];
        $testDataYAML = Yaml::dump($testData);
        $serverMock = $this->_getServerReturningYAMLData($testDataYAML);
        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($testDataYAML));


        $command = $this->commandFactory->create(Command::COMMAND_STATS_JOB, [self::TEST_ID]);
        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertThat($response->getData(), $this->isType('array'));
    }

    // STATS-TUBE COMMAND //
    /**
     * @param mixed $tubeName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testStatsTubeCommand_Construct_invalidTubeName_ThrowsInvalidNameException($tubeName)
    {
        $this->commandFactory->create(Command::COMMAND_STATS_TUBE, [$tubeName]);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testStatsTubeCommand_Construct_validTubeName_noException($tubeName)
    {
        $command = $this->commandFactory->create(Command::COMMAND_STATS_TUBE, [$tubeName]);


        $this->assertInstanceOf(Command::class, $command);
    }

    public function testStatsTubeCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_STATS_TUBE, self::TEST_TUBE);


        $command = $this->commandFactory->create(Command::COMMAND_STATS_TUBE, [self::TEST_TUBE]);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testStatsTubeCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_STATS_TUBE, [self::TEST_TUBE])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    public function testStatsTubeCommand_ParseResponse_OKResponse_callsParent()
    {
        $testData = ['hello'];
        $testDataYAML = Yaml::dump($testData);
        $serverMock = $this->_getServerReturningYAMLData($testDataYAML);
        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($testDataYAML));


        $command = $this->commandFactory->create(Command::COMMAND_STATS_TUBE, [self::TEST_TUBE]);
        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertThat($response->getData(), $this->isType('array'));
    }

    // TOUCH COMMAND //
    public function testTouchCommand_GetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_TOUCH, self::TEST_ID);


        $touchCommand = $this->commandFactory->create(Command::COMMAND_TOUCH, [self::TEST_ID]);


        $this->assertEquals($expected, $touchCommand->getCommandLine());
    }

    public function testTouchCommand_ParseResponse_touched_returnsResponseWithoutData()
    {
        $touchCommand = $this->commandFactory->create(Command::COMMAND_TOUCH, [self::TEST_ID]);


        $response = $touchCommand->parseResponse(Response::RESPONSE_TOUCHED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_TOUCHED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testTouchCommand_ParseResponse_unexpectedResponse_throwsException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_TOUCH, [self::TEST_ID])
            ->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testTouchCommand_ParseResponse_notFoundFailure_throwsNotFoundException()
    {
        $this->commandFactory
            ->create(Command::COMMAND_TOUCH, [self::TEST_ID])
            ->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    // USE COMMAND //
    public function testUseCommand_GetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_USE, $tubeName);


        $commandLine = $this->commandFactory
            ->create(Command::COMMAND_USE, [$tubeName])
            ->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testUseCommand_ParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_USING, Beanie::DEFAULT_TUBE);
        $useCommand = $this->commandFactory->create(Command::COMMAND_USE, [Beanie::DEFAULT_TUBE]);


        $response = $useCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_USING, $response->getName());
        $this->assertEquals(Beanie::DEFAULT_TUBE, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // WATCH COMMAND //
    public function testWatchCommand_GetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_WATCH, $tubeName);


        $commandLine = $this->commandFactory
            ->create(Command::COMMAND_WATCH, [$tubeName])
            ->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testWatchCommand_ParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_WATCHING, 1);
        $watchCommand = $this->commandFactory->create(Command::COMMAND_WATCH, [Beanie::DEFAULT_TUBE]);


        $response = $watchCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_WATCHING, $response->getName());
        $this->assertEquals(1, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    // DATA PROVIDERS //
    public function putCommand_failureResponses()
    {
        return [
            'draining' => [
                Response::FAILURE_DRAINING,
                DrainingException::class,
                DrainingException::DEFAULT_CODE
            ],
            'job too big' => [
                Response::FAILURE_JOB_TOO_BIG,
                JobTooBigEXception::class,
                JobTooBigException::DEFAULT_CODE
            ],
            'expected crlf' => [
                Response::FAILURE_EXPECTED_CRLF,
                ExpectedCRLFException::class,
                ExpectedCRLFException::DEFAULT_CODE
            ]
        ];
    }

    public function putCommand_successResponses()
    {
        return [
            'inserted' => [
                Response::RESPONSE_INSERTED . ' 1',
                Response::RESPONSE_INSERTED,
                1
            ],
            'buried' => [
                Response::RESPONSE_BURIED . ' 2',
                Response::RESPONSE_BURIED,
                2
            ]
        ];
    }

    public function validNamesProvider()
    {
        return [
            ['default'],
            ['(why-_such'],
            ['_N4me)s$'],
            ['A-Za-z0-9+/;.$_()-'],
            [str_repeat('a', 200)]
        ];
    }

    public function invalidNamesProvider()
    {
        return [
            [true],
            [10],
            [''],
            [new \stdClass()],
            ['contains spaces'],
            ['-startsWithHyphen'],
            ['contains@illegalCharacter'],
            [str_repeat('a', 201)]
        ];
    }
}
