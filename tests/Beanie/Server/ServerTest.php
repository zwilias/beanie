<?php


namespace Beanie\Server;

use Beanie\Command\GenericCommand;
use Beanie\Command\Response;
use Beanie\Manager;
use Beanie\Tube\TubeStatus;

require_once 'MockNative_TestCase.php';

class ServerTest extends MockNative_TestCase
{
    public function testConstruct_noArgs_usesDefaults()
    {
        $this->_socketCreateSuccess();


        $server = new Server();


        $this->assertEquals('127.0.0.1:11300', (string)$server);
        $this->assertInstanceOf(TubeStatus::class, $server->getTubeStatus());
    }

    public function testConstruct_withArgs_usesArgs()
    {
        $this->_socketCreateSuccess();
        $hostName = 'testhost';
        $port = 10001;


        $server = new Server($hostName, $port);


        $this->assertEquals($hostName . ':' . $port, (string)$server);
    }

    public function testGetManager_returnsManager()
    {
        $this->_socketCreateSuccess();


        $server = new Server();
        $manager = $server->getManager();


        $this->assertInstanceOf(Manager::class, $manager);
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 666
     * @expectedExceptionMessage nope
     */
    public function testConstruct_socketCreationFails_ExceptionPropagated()
    {
        $this->_socketCreateFail(666, 'nope');


        new Server();
    }

    /**
     * @param bool $isConnected
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $connectCondition
     * @param string $data
     *
     * @dataProvider readDataConditionsProvider
     */
    public function testReadData_socketConditions_readsData(
        $isConnected,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $connectCondition,
        $data
    ) {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Socket $socket */
        $socket = $this
            ->getMockBuilder(Socket::class)
            ->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connect', 'readData'])
            ->getMock()
        ;

        $socket
            ->expects($this->once())
            ->method('isConnected')
            ->willReturn($isConnected)
        ;

        $socket
            ->expects($connectCondition)
            ->method('connect')
            ->willReturn(true)
        ;

        $socket
            ->expects($this->once())
            ->method('readData')
            ->with(strlen($data) + Server::EOL_LENGTH)
            ->willReturn($data . Server::EOL)
        ;

        $server = new Server();
        $server->setSocket($socket);


        $actualData = $server->readData(strlen($data));


        $this->assertEquals($data, $actualData);
    }

    public function readDataConditionsProvider()
    {
        return [
            [false, $this->once(), 'random data'],
            [true, $this->never(), 'other data!']
        ];
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 123
     * @expectedExceptionMessage fail
     */
    public function testReadData_connectionFailure_throwsSocketException()
    {
        $this->_socketCreateSuccess();
        $this->_socketConnectFail(Server::DEFAULT_HOST, Server::DEFAULT_PORT, 123, 'fail');

        $server = new Server();


        $server->readData(123);
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 123
     * @expectedExceptionMessage nope
     */
    public function testReadData_connectionDropped_throwsSocketException()
    {
        $this->_socketCreateSuccess();
        $this->_socketConnectSuccess();
        $this->_setSocketError(123, 'nope');

        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_read')
            ->willReturn(false);


        $server = new Server();


        $server->readData(321);
    }

    public function testDispatchCommandWithoutData_writesCommand_retrievesResponse()
    {
        $responseLine = 'great job';
        $commandLine = 'test command';

        $server = new Server();
        $expectedResponse = new Response(null, null, $server);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\Command $command */
        $command = $this
            ->getMockBuilder(GenericCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommandLine', 'parseResponse', 'hasData'])
            ->getMockForAbstractClass()
        ;

        $command
            ->expects($this->once())
            ->method('getCommandLine')
            ->willReturn($commandLine)
        ;

        $command
            ->expects($this->once())
            ->method('hasData')
            ->willReturn(false)
        ;

        $command
            ->expects($this->once())
            ->method('parseResponse')
            ->with($responseLine, $server)
            ->willReturn($expectedResponse)
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Socket $socket */
        $socket = $this
            ->getMockBuilder(Socket::class)
            ->disableOriginalConstructor()
            ->setMethods(['isConnected', 'readLine', 'write'])
            ->getMock()
        ;

        $socket
            ->expects($this->atLeastOnce())
            ->method('isConnected')
            ->willReturn(true)
        ;

        $socket
            ->expects($this->once())
            ->method('write')
            ->with($commandLine . Server::EOL)
            ->willReturn(true)
        ;

        $socket
            ->expects($this->once())
            ->method('readLine')
            ->with(Server::EOL)
            ->willReturn($responseLine . Server::EOL)
        ;


        $server->setSocket($socket);
        $response = $server->dispatchCommand($command);


        $this->assertEquals($expectedResponse, $response->invoke());
    }

    public function testDispatchCommandWithData_writesCommandAndData_retrievesResponse()
    {
        $responseLine = 'great job';
        $commandLine = 'test command';
        $commandData = 'test data';

        $server = new Server();
        $expectedResponse = new Response(null, null, $server);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\Command $command */
        $command = $this
            ->getMockBuilder(GenericCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommandLine', 'parseResponse', 'hasData', 'getData'])
            ->getMockForAbstractClass()
        ;

        $command
            ->expects($this->once())
            ->method('getCommandLine')
            ->willReturn($commandLine)
        ;

        $command
            ->expects($this->once())
            ->method('hasData')
            ->willReturn(true)
        ;

        $command
            ->expects($this->once())
            ->method('getData')
            ->willReturn($commandData)
        ;

        $command
            ->expects($this->once())
            ->method('parseResponse')
            ->with($responseLine, $server)
            ->willReturn($expectedResponse)
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Socket $socket */
        $socket = $this
            ->getMockBuilder(Socket::class)
            ->disableOriginalConstructor()
            ->setMethods(['isConnected', 'readLine', 'write'])
            ->getMock()
        ;

        $socket
            ->expects($this->atLeastOnce())
            ->method('isConnected')
            ->willReturn(true)
        ;

        $socket
            ->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                [$commandLine . Server::EOL],
                [$commandData . Server::EOL]
            )
            ->willReturn(true)
        ;

        $socket
            ->expects($this->once())
            ->method('readLine')
            ->with(Server::EOL)
            ->willReturn($responseLine . Server::EOL)
        ;


        $server->setSocket($socket);
        $response = $server->dispatchCommand($command);


        $this->assertEquals($expectedResponse, $response->invoke());
    }

    public function testTransformTubeStatusTo_willDispatchCommand()
    {
        $tubeStatus = new TubeStatus();
        $tubeStatus->setCurrentTube('test')->addWatchedTube('test');

        /** @var \PHPUnit_Framework_MockObject_MockObject|Server $serverStub */
        $serverStub = $this->getMockBuilder(Server::class)
            ->setMethods(['dispatchCommand'])
            ->getMock();

        $serverStub->expects($this->exactly(2))
            ->method('dispatchCommand')
            ->willReturn(
                $this->getMockBuilder(ResponseOath::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['invoke'])
                    ->getMock()
            );


        $serverStub->transformTubeStatusTo($tubeStatus);
    }
}
