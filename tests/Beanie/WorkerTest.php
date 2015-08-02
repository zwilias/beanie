<?php


namespace Beanie;

use Beanie\Command\CommandInterface;
use Beanie\Command\Response;
use Beanie\Exception\TimedOutException;
use Beanie\Job\Job;
use Beanie\Job\JobOath;
use Beanie\Tube\TubeStatus;

require_once 'WithServerMock_TestCase.php';

class WorkerTest extends WithServerMock_TestCase
{
    public function testGetServer_returnsServer()
    {
        $serverMock = $this->getServerMock();


        $worker = new Worker($serverMock);


        $this->assertSame($serverMock, $worker->getServer());
    }

    public function testTransformTubeStatusTo_onlyUpdatesTubeStatus()
    {
        $tubeStatus = new TubeStatus();
        $tubeStatus->setCurrentTube('test')->addWatchedTube('test');

        $serverMock = $this->getServerMock(['dispatchCommand']);
        $serverMock->expects($this->never())->method('dispatchCommand');


        $worker = new Worker($serverMock);
        $worker->transformTubeStatusTo($tubeStatus, TubeStatus::TRANSFORM_WATCHED);


        $this->assertEquals(Beanie::DEFAULT_TUBE, $worker->getTubeStatus()->getCurrentTube());
        $this->assertEquals($tubeStatus->getWatchedTubes(), $worker->getTubeStatus()->getWatchedTubes());
    }

    public function testWatch_tube_watchesTube()
    {
        $tube = 'test';


        $worker = new Worker($this->getServerMock());
        $worker->watch($tube);


        $this->assertContains($tube, $worker->getTubeStatus()->getWatchedTubes());
        $this->assertGreaterThan(1, count($worker->getTubeStatus()->getWatchedTubes()));
    }

    public function testWatch_onlyThisTube_watchesOnlyThisTube()
    {
        $tube = 'test';


        $worker = new Worker($this->getServerMock());
        $worker->watch($tube, true);


        $this->assertEquals([$tube], $worker->getTubeStatus()->getWatchedTubes());
    }

    public function testIgnoreTube_removesWatchedTube()
    {
        $tubeToIgnore = 'test';
        $tubesToAdd = ['test1', 'test2', $tubeToIgnore];


        $worker = new Worker($this->getServerMock());
        foreach ($tubesToAdd as $tube) {
            $worker->watch($tube);
        }

        $worker->ignore($tubeToIgnore);


        $this->assertNotContains($tubeToIgnore, $worker->getTubeStatus()->getWatchedTubes());
    }

    public function testQuit_firesQuitCommand()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);
        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (CommandInterface $command) {
                return $command->getCommandLine() === CommandInterface::COMMAND_QUIT;
            }))
            ->willReturnSelf();

        $worker = new Worker($serverMock);


        $worker->quit();
    }

    public function testReserve_returnsJob()
    {
        $serverMock = $this->getServerMock(['dispatchCommand', 'transformTubeStatusTo']);

        $response = new Response(Response::RESPONSE_RESERVED, [
            'id' => 12,
            'data' => 'hi'
        ], $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->with($this->isInstanceOf(TubeStatus::class))
            ->willReturn($serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (CommandInterface $command) {
                return $command->getCommandLine() === CommandInterface::COMMAND_RESERVE;
            }))
            ->willReturn($this->oath($response));


        $worker = new Worker($serverMock);
        $job = $worker->reserve();


        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals(Job::STATE_RESERVED, $job->getState());
        $this->assertEquals(12, $job->getId());
        $this->assertEquals('hi', $job->getData());
    }

    public function testReserveOath_returnsJobOath()
    {
        $serverMock = $this->getServerMock(['dispatchCommand', 'transformTubeStatusTo']);

        $serverMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->with($this->isInstanceOf(TubeStatus::class))
            ->willReturn($serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (CommandInterface $command) {
                return $command->getCommandLine() === CommandInterface::COMMAND_RESERVE;
            }))
            ->willReturn($this->_getResponseOathMock());


        $worker = new Worker($serverMock);
        $jobOath = $worker->reserveOath();


        $this->assertInstanceOf(JobOath::class, $jobOath);
    }

    public function testReserve_withTimeout_returnsJob()
    {
        $serverMock = $this->getServerMock(['dispatchCommand', 'transformTubeStatusTo']);

        $response = new Response(Response::RESPONSE_RESERVED, [
            'id' => 12,
            'data' => 'hi'
        ], $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->with($this->isInstanceOf(TubeStatus::class))
            ->willReturn($serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (CommandInterface $command) {
                return $command->getCommandLine() === CommandInterface::COMMAND_RESERVE_WITH_TIMEOUT . ' 12';
            }))
            ->willReturn($this->oath($response));


        $worker = new Worker($serverMock);
        $job = $worker->reserve(12);


        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals(Job::STATE_RESERVED, $job->getState());
        $this->assertEquals(12, $job->getId());
        $this->assertEquals('hi', $job->getData());
    }

    public function testReserve_withTimeout_timeOutException_returnsNull()
    {
        $serverMock = $this->getServerMock(['dispatchCommand', 'transformTubeStatusTo']);

        $serverMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->with($this->isInstanceOf(TubeStatus::class))
            ->willReturn($serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (CommandInterface $command) {
                return $command->getCommandLine() === CommandInterface::COMMAND_RESERVE_WITH_TIMEOUT . ' 12';
            }))
            ->willThrowException(new TimedOutException($serverMock));


        $worker = new Worker($serverMock);
        $job = $worker->reserve(12);


        $this->assertNull($job);
    }

    public function testToString_callsServerToString()
    {
        $serverMock = $this->getServerMock();
        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test');


        $worker = new Worker($serverMock);


        $this->assertEquals('test', (string)$worker);
    }

    public function testReconnect_callsServerConnect()
    {
        $serverMock = $this->getServerMock(['connect']);
        $serverMock
            ->expects($this->once())
            ->method('connect');


        $worker = new Worker($serverMock);


        $worker->reconnect();
    }

    public function testDisconnect_callsServerDisconnect()
    {
        $serverMock = $this->getServerMock(['disconnect']);
        $serverMock
            ->expects($this->once())
            ->method('disconnect');

        $worker = new Worker($serverMock);


        $worker->disconnect();
    }
}
