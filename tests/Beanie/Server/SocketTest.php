<?php


namespace Beanie\Server;

require_once __DIR__ . '/../../nativeFunctions.php';

class SocketTest extends \MockNativeTestCase
{
    public function testConstruct_noArgs_createsSocketWithDefaults()
    {
        $this->_socketCreateSuccess();


        $socket = new Socket();


        $this->assertEquals(Server::DEFAULT_HOST, $socket->getHostname());
        $this->assertEquals(Server::DEFAULT_PORT, $socket->getPort());
        $this->assertFalse($socket->isConnected());
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
}
