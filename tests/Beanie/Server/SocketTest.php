<?php


namespace Beanie\Server;

require_once 'MockNative_TestCase.php';

class SocketTest extends MockNative_TestCase
{
    public function testConstruct_noArgs_createsSocketWithDefaults()
    {
        $this->_socketCreateSuccess();


        $socket = new Socket();


        $this->assertEquals(Server::DEFAULT_HOST, $socket->getHostname());
        $this->assertEquals(Server::DEFAULT_PORT, $socket->getPort());
        $this->assertFalse($socket->isConnected());
        $this->assertNotNull($socket->getRaw());
    }

    public function testConstruct_withArgs_createsSocketWithArgs()
    {
        $this->_socketCreateSuccess();

        $hostName = 'testHostName';
        $port = 666;


        $socket = new Socket($hostName, $port);


        $this->assertEquals($hostName, $socket->getHostname());
        $this->assertEquals($port, $socket->getPort());
        $this->assertFalse($socket->isConnected());
        $this->assertNotNull($socket->getRaw());
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 123
     * @expectedExceptionMessage testerror
     */
    public function testConstruct_socketCreationFails_throwsException()
    {
        $this->_socketCreateFail(123, 'testerror');


        new Socket();
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionMessage Socket is not connected.
     */
    public function testWrite_beforeCallingConnect_throwsException()
    {
        $this->_socketCreateSuccess();


        $socket = new Socket();


        $socket->write('');
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 456
     * @expectedExceptionMessage test
     */
    public function testConnect_fail_throwsException()
    {
        $this->_socketCreateSuccess();
        $this->_socketConnectFail(Server::DEFAULT_HOST, Server::DEFAULT_PORT, 456, 'test');


        $socket = new Socket();
        $socket->connect();
    }

    public function testConnect_usesHostAndPort()
    {
        $host = 'testHost';
        $port = 6696;

        $this->_socketCreateSuccess();
        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_connect')
            ->with($this->anything(), $host, $port)
            ->willReturn(true);


        $socket = new Socket($host, $port);
        $socket->connect();


        $this->assertTrue($socket->isConnected());
    }

    public function testWrite_writesDataToConnectedSocket()
    {
        $data = 'testdata';

        $this->_socketCreateSuccess();
        $this->_socketConnectSuccess();

        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_write')
            ->with($this->anything(), $data, strlen($data))
            ->willReturn(strlen($data))
        ;


        $socket = new Socket();
        $socket->connect();
        $written = $socket->write($data);


        $this->assertEquals(strlen($data), $written);
    }

    /**
     * @expectedException \Beanie\Exception\SocketException
     * @expectedExceptionCode 666
     * @expectedExceptionMessage nope
     */
    public function testWrite_writeFails_throwsException()
    {
        $data = 'testdata';

        $this->_socketCreateSuccess();
        $this->_socketConnectSuccess();
        $this->_setSocketError(666, 'nope');

        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_write')
            ->with($this->anything(), $data, strlen($data))
            ->willReturn(false)
        ;

        $socket = new Socket();
        $socket->connect();
        $socket->write($data);
    }

    public function testWrite_writePartial_continuesCorrectly()
    {
        $data = [
            'part 1',
            'and part 2',
            'part 3'
        ];

        $this->_socketCreateSuccess();
        $this->_socketConnectSuccess();

        $this->_getNativeFunctionMock()
            ->expects($this->exactly(3))
            ->method('socket_write')
            ->withConsecutive(
                [$this->anything(), join('', array_slice($data, 0)), strlen(join('', array_slice($data, 0)))],
                [$this->anything(), join('', array_slice($data, 1)), strlen(join('', array_slice($data, 1)))],
                [$this->anything(), join('', array_slice($data, 2)), strlen(join('', array_slice($data, 2)))]
            )
            ->willReturnOnConsecutiveCalls(strlen($data[0]), strlen($data[1]), strlen($data[2]))
        ;


        $socket = new Socket();
        $socket->connect();
        $written = $socket->write(join('', $data));


        $this->assertEquals(strlen(join('', $data)), $written);
    }
}
