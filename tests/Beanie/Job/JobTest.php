<?php


namespace Beanie\Job;

use Beanie\Beanie;
use Beanie\Command\Command;
use Beanie\Command\Response;
use Beanie\WithServerMock_TestCase;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

class JobTest extends WithServerMock_TestCase
{
    const TEST_ID = 666;

    public function testKick_dispatchesKickCommand()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() === sprintf('%s %s', Command::COMMAND_KICK_JOB, self::TEST_ID);
            }))
            ->willReturn($this->_getResponseOathMock());

        $job = new Job(self::TEST_ID, null, $serverMock);


        $job->kick();
    }

    public function testTouch_dispatchesTouchCommand()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() === sprintf('%s %s', Command::COMMAND_TOUCH, self::TEST_ID);
            }))
            ->willReturn($this->_getResponseOathMock());

        $job = new Job(self::TEST_ID, null, $serverMock);


        $job->touch();
    }

    public function testDelete_dispatchesDeleteCommand()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() === sprintf('%s %s', Command::COMMAND_DELETE, self::TEST_ID);
            }))
            ->willReturn($this->_getResponseOathMock());

        $job = new Job(self::TEST_ID, null, $serverMock);


        $job->delete();


        $this->assertEquals(Job::STATE_DELETED, $job->getState());
    }

    public function testStats_dispatchesStatsCommand_returnsStats()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);
        $stats = ['some' => 'stats'];
        $response = new Response(Response::RESPONSE_OK, $stats, $serverMock);

        $responseOath = $this->_getResponseOathMock();
        $responseOath
            ->expects($this->once())
            ->method('invoke')
            ->willReturn($response);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() === sprintf('%s %s', Command::COMMAND_STATS_JOB, self::TEST_ID);
            }))
            ->willReturn($responseOath);

        $job = new Job(self::TEST_ID, null, $serverMock);


        $jobStats = $job->stats();


        $this->assertEquals($stats, $jobStats);
    }

    public function testBury_dispatchesBuryCommand()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() === sprintf('%s %s %s', Command::COMMAND_BURY, self::TEST_ID, Beanie::DEFAULT_PRIORITY);
            }))
            ->willReturn($this->_getResponseOathMock());

        $job = new Job(self::TEST_ID, null, $serverMock);


        $job->bury();


        $this->assertEquals(Job::STATE_BURIED, $job->getState());
    }

    public function testBury_dispatchesBuryCommand_usesPriority()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);
        $priority = 99;

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) use ($priority) {
                return $command->getCommandLine() === sprintf('%s %s %s', Command::COMMAND_BURY, self::TEST_ID, $priority);
            }))
            ->willReturn($this->_getResponseOathMock());

        $job = new Job(self::TEST_ID, null, $serverMock);


        $job->bury($priority);


        $this->assertEquals(Job::STATE_BURIED, $job->getState());
    }

    /**
     * @param array $arguments
     * @param string $responseName
     * @param string $expectedState
     * @dataProvider releaseArgumentsProvider
     */
    public function testReleases_dispatchesReleaseCommand_usesArgumentsAndResponseName(array $arguments, $responseName, $expectedState)
    {
        $defaultArgs = [Beanie::DEFAULT_PRIORITY, Beanie::DEFAULT_DELAY];
        $actualArgs = $arguments + $defaultArgs;

        $serverMock = $this->getServerMock(['dispatchCommand']);
        $response = new Response($responseName, null, $serverMock);

        $oath = $this->_getResponseOathMock();
        $oath
            ->expects($this->once())
            ->method('invoke')
            ->willReturn($response);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) use ($actualArgs) {
                return $command->getCommandLine() === sprintf('%s %s %s', Command::COMMAND_RELEASE, self::TEST_ID, join(' ', $actualArgs));
            }))
            ->willReturn($oath);

        $job = new Job(self::TEST_ID, null, $serverMock);


        call_user_func_array([$job, 'release'], $arguments);


        $this->assertEquals($expectedState, $job->getState());
    }

    public function releaseArgumentsProvider()
    {
        $stateMap = [
            Response::RESPONSE_RELEASED => Job::STATE_RELEASED,
            Response::RESPONSE_BURIED => Job::STATE_BURIED
        ];

        $args = [
            [],
            [5],
            [66, 12]
        ];

        $result = [];

        foreach ($stateMap as $responseName => $expectedState) {
            foreach ($args as $arguments) {
                $result[] = [
                    $arguments,
                    $responseName,
                    $expectedState
                ];
            }
        }

        return $result;
    }
}
