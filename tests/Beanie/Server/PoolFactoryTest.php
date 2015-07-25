<?php


namespace Beanie\Server;

require_once 'MockNative_TestCase.php';

class PoolFactoryTest extends MockNative_TestCase
{
    public function testCreateServer_createsServer()
    {
        $this->_socketCreateSuccess();


        $factory = new PoolFactory();
        $server = $factory->createServer(Server::DEFAULT_HOST, Server::DEFAULT_PORT);


        $this->assertInstanceOf(Server::class, $server);
    }

    public function testCreate_createsPool()
    {
        $serverNames = [
            'hi',
            'there',
            'you'
        ];

        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->setMethods(['__toString'])
            ->disableOriginalConstructor()
            ->getMock();

        $serverMock->expects($this->exactly(3))
            ->method('__toString')
            ->willReturnOnConsecutiveCalls('hi', 'there', 'you');

        /** @var \PHPUnit_Framework_MockObject_MockObject|PoolFactory $factoryMock */
        $factoryMock = $this->getMockBuilder(PoolFactory::class)
            ->setMethods(['createServer'])
            ->getMock();

        $factoryMock->expects($this->exactly(3))
            ->method('createServer')
            ->willReturn($serverMock);


        $pool = $factoryMock->create(['hi', 'there', 'you']);


        $this->assertEquals($serverNames, array_keys($pool->getServers()));
    }
}
