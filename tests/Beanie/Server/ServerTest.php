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
}
