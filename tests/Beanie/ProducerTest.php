<?php


namespace Beanie;

require_once 'WithServerMock_TestCase.php';

use Beanie\Command\Command;
use Beanie\Command\Response;
use Beanie\Job\Job;
use Beanie\Server\Pool;
use Beanie\Server\Server;
use Beanie\Tube\TubeStatus;

class ProducerTest extends WithServerMock_TestCase
{
    public function testGetPool_returnsPool()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this
            ->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();


        $producer = new Producer($poolMock);


        $this->assertSame($poolMock, $producer->getPool());
    }

    public function testUse_onlyUpdatesTubeStatus()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this
            ->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatchCommand'])
            ->getMock();

        $poolMock
            ->expects($this->never())
            ->method('dispatchCommand');


        $producer = new Producer($poolMock);
        $producer->useTube('TEST-TUBE');


        $this->assertEquals('TEST-TUBE', $producer->getTubeStatus()->getCurrentTube());
    }

    public function testTransformTubeStatusTo_updatesTubeStatus()
    {
        $testTube = new TubeStatus();
        $testTube->setCurrentTube('TEST');
        $testTube->addWatchedTube('TEST');

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this
            ->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatchCommand'])
            ->getMock();

        $poolMock
            ->expects($this->never())
            ->method('dispatchCommand');


        $producer = new Producer($poolMock);
        $producer->transformTubeStatusTo($testTube, TubeStatus::TRANSFORM_BOTH);


        $this->assertEquals($testTube->getCurrentTube(), $producer->getTubeStatus()->getCurrentTube());
        $this->assertEquals($testTube->getWatchedTubes(), $producer->getTubeStatus()->getWatchedTubes());
    }

    /**
     * @param string $responseName
     * @param int $jobId
     * @param array $arguments
     * @param string $expectedJobState
     * @dataProvider createJobProvider
     */
    public function testPut_createsJob_correctStatus(
        $responseName,
        $jobId,
        array $arguments,
        $expectedJobState
    ) {
        $jobData = $arguments[0];

        $argumentDefaults = [
            null,
            Beanie::DEFAULT_PRIORITY,
            Beanie::DEFAULT_DELAY,
            Beanie::DEFAULT_TIME_TO_RUN
        ];

        $finalArguments = $arguments + $argumentDefaults;

        $expectedCommandLine = join(' ', [
            Command::COMMAND_PUT,
            $finalArguments[1],
            $finalArguments[2],
            $finalArguments[3],
            strlen($jobData)
        ]);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Server $serverMock */
        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this
            ->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatchCommand', 'transformTubeStatusTo'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->with($this->isInstanceOf(TubeStatus::class))
            ->willReturn($poolMock);

        $poolMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) use ($expectedCommandLine, $jobData) {
                return
                    $command->getCommandLine() == $expectedCommandLine &&
                    $command->hasData() === true &&
                    $command->getData() === $jobData;
            }))
            ->willReturn($this->oath(new Response($responseName, $jobId, $serverMock)));


        $producer = new Producer($poolMock);
        /** @var Job $job */
        $job = call_user_func_array([$producer, 'put'], $arguments);


        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($jobId, $job->getId());
        $this->assertEquals($jobData, $job->getData());
        $this->assertEquals($expectedJobState, $job->getState());
    }

    /*
     * @param string $responseName
     * @param int $jobId
     * @param array $arguments
     * @param string $expectedJobState
     */
    public function createJobProvider()
    {
        return [
            'inserted-defaults' => [
                Response::RESPONSE_INSERTED,
                123,
                ['data'],
                Job::STATE_RELEASED
            ],
            'inserted-withPrio' => [
                Response::RESPONSE_INSERTED,
                234,
                ['moreData', 5],
                Job::STATE_RELEASED
            ],
            'inserted-withPrio-withDelay' => [
                Response::RESPONSE_INSERTED,
                345,
                ['moreData', 10, 4],
                Job::STATE_RELEASED
            ],
            'inserted-withPrio-withDelay-withTtr' => [
                Response::RESPONSE_INSERTED,
                456,
                ['even more data', 30, 23, 54],
                Job::STATE_RELEASED
            ],
            'buried-defaults' => [
                Response::RESPONSE_BURIED,
                9,
                ['nope'],
                Job::STATE_BURIED
            ]
        ];
    }
}
