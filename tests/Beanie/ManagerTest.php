<?php


namespace Beanie;

use Beanie\Command\Command;
use Beanie\Command\Response;
use Beanie\Exception\NotFoundException;
use Beanie\Job\Job;
use Beanie\Tube\Tube;

require_once 'WithServerMock_TestCase.php';

class ManagerTest extends WithServerMock_TestCase
{
    public function testStats_executesStatsCommand_returnsResponseData()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $data = ['test'];
        $response = new Response(Response::RESPONSE_OK, $data, $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() == Command::COMMAND_STATS;
            }))
            ->willReturn($response)
        ;


        $manager = new Manager($serverMock);
        $stats = $manager->stats();


        $this->assertEquals($data, $stats);
    }

    public function testPeek_executesPeekCommand_returnsJob()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $jobId = 5;
        $data = ['id' => $jobId, 'data' => 'test'];
        $response = new Response(Response::RESPONSE_FOUND, $data, $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) use ($jobId) {
                return $command->getCommandLine() == sprintf('%s %s', Command::COMMAND_PEEK, $jobId);
            }))
            ->willReturn($response)
        ;


        $manager = new Manager($serverMock);
        $job = $manager->peek($jobId);


        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($jobId, $job->getId());
        $this->assertEquals('test', $job->getData());
        $this->assertEquals(Job::STATE_UNKNOWN, $job->getState());
    }

    public function testPeek_notFoundFailure_retursNull()
    {
        $jobId = 5;
        $serverMock = $this->getServerMock(['dispatchCommand']);
        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) use ($jobId) {
                return $command->getCommandLine() == sprintf('%s %s', Command::COMMAND_PEEK, $jobId);
            }))
            ->willThrowException(new NotFoundException($serverMock));
        ;


        $manager = new Manager($serverMock);


        $this->assertNull($manager->peek($jobId));
    }

    public function testTubes_returnsArrayOfTubeObjects()
    {
        $tubeNames = ['tube1', 'tube2', 'tube3'];

        $serverMock = $this->getServerMock(['dispatchCommand']);

        $response = new Response(Response::RESPONSE_OK, $tubeNames, $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() == Command::COMMAND_LIST_TUBES;
            }))
            ->willReturn($response)
        ;


        $manager = new Manager($serverMock);
        $tubes = $manager->tubes();


        $this->assertCount(count($tubeNames), $tubes);

        foreach ($tubes as $tube) {
            $this->assertInstanceOf(Tube::class, $tube);
            $this->assertContains($tube->getName(), $tubeNames);
        }
    }
}
