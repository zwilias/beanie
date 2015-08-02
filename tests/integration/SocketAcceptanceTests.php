<?php


/**
 * Class SocketAcceptanceTests
 * @coversNothing
 */
class SocketAcceptanceTests extends PHPUnit_Framework_TestCase
{
    protected $socket;
    protected $serverName;

    public function setUp()
    {
        $this->socket = socket_create_listen(0);
        socket_getsockname($this->socket, $host, $port);
        $this->serverName = sprintf('%s:%s', $host, $port);
    }

    public function tearDown()
    {
        @socket_close($this->socket);
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 32
     */
    public function testProducer_closedSocketBeforeWriting_throwsSocketException()
    {
        $producer = \Beanie\Beanie::pool([$this->serverName])->producer();
        $server = $producer->getPool()->getServer($this->serverName);
        $server->connect();

        $acceptedConnection = socket_accept($this->socket);
        socket_close($acceptedConnection);

        $producer->put('test');
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 61
     */
    public function testProducer_noSocket_throwsSocketException()
    {
        socket_close($this->socket);
        $producer = \Beanie\Beanie::pool([$this->serverName])->producer();
        $producer->getPool()->getServer($this->serverName)->connect();
    }

    public function testProducer_putCreatesJob()
    {
        $testData = 'test';

        $producer = \Beanie\Beanie::pool([$this->serverName])->producer();
        $server = $producer->getPool()->getServer($this->serverName);
        $server->connect();

        $acceptedConnection = socket_accept($this->socket);

        socket_set_nonblock($acceptedConnection);
        socket_write($acceptedConnection, 'INSERTED 1' . "\r\n");

        $job = $producer->put($testData);

        $command = socket_read($acceptedConnection, 1024);

        $this->assertEquals(
            sprintf(
                '%s %s %s %s %s%s%s%s',
                \Beanie\Command\CommandInterface::COMMAND_PUT,
                \Beanie\Beanie::DEFAULT_PRIORITY,
                \Beanie\Beanie::DEFAULT_DELAY,
                \Beanie\Beanie::DEFAULT_TIME_TO_RUN,
                strlen($testData),
                "\r\n",
                $testData,
                "\r\n"
            ),
            $command
        );

        $this->assertInstanceOf(\Beanie\Job\Job::class, $job);
        $this->assertEquals(1, $job->getId());
        $this->assertEquals('test', $job->getData());
        $this->assertEquals(\Beanie\Job\Job::STATE_RELEASED, $job->getState());

        socket_close($acceptedConnection);
    }

    public function testWorker_reserve_receivesJob()
    {
        $jobData = 'test';
        $response = sprintf('%s %s %s', \Beanie\Command\Response::RESPONSE_RESERVED, 1, strlen($jobData));

        $worker = \Beanie\Beanie::pool([$this->serverName])->worker();
        $worker->getServer()->connect();

        $acceptedConnection = socket_accept($this->socket);
        socket_set_nonblock($acceptedConnection);
        socket_write($acceptedConnection, $response  . "\r\n");
        socket_write($acceptedConnection, $jobData . "\r\n");

        $job = $worker->reserve();

        $command = socket_read($acceptedConnection, 1024);

        $this->assertEquals(sprintf(
            '%s' . "\r\n",
            \Beanie\Command\CommandInterface::COMMAND_RESERVE
        ), $command);

        $this->assertInstanceOf(\Beanie\Job\Job::class, $job);
        $this->assertEquals(1, $job->getId());
        $this->assertEquals($jobData, $job->getData());
        $this->assertEquals(\Beanie\Job\Job::STATE_RESERVED, $job->getState());

        socket_close($acceptedConnection);
    }
}
