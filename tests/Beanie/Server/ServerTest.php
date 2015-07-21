<?php


namespace Beanie\Server;

require_once 'MockNative_TestCase.php';

class ServerTest extends MockNative_TestCase
{
    public function testConstruct_noArgs_usesDefaults()
    {
        $this->_socketCreateSuccess();


        $server = new Server();


        $this->assertEquals('127.0.0.1:11300', (string)$server);
    }

    public function testConstruct_withArgs_usesArgs()
    {
        $this->_socketCreateSuccess();
        $hostName = 'testhost';
        $port = 10001;


        $server = new Server($hostName, $port);


        $this->assertEquals($hostName . ':' . $port, (string)$server);
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
            ->getMockBuilder('\Beanie\Server\Socket')
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

    public function testReadLine_readsLineFromSocket_stripsEOL()
    {
        $expected = 'This is a line';

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Socket $socket */
        $socket = $this
            ->getMockBuilder('\Beanie\Server\Socket')
            ->disableOriginalConstructor()
            ->setMethods(['isConnected', 'readLine'])
            ->getMock()
        ;

        $socket
            ->expects($this->once())
            ->method('isConnected')
            ->willReturn(true)
        ;

        $socket
            ->expects($this->once())
            ->method('readLine')
            ->with(Server::EOL)
            ->willReturn($expected . Server::EOL)
        ;


        $server = new Server();
        $server->setSocket($socket);
        $data = $server->readLine();


        $this->assertEquals($expected, $data);
    }
}
