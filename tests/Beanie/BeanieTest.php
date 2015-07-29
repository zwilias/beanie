<?php


namespace Beanie;


use Beanie\Server\Pool;
use Beanie\Server\PoolFactory;
use Beanie\Server\Server;

class BeanieTest extends \PHPUnit_Framework_TestCase
{
    public function testPool_createsInstanceWithPool()
    {
        $servers = ['test1', 'test2'];

        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $poolFactoryMock = $this->getMockBuilder(PoolFactory::class)
            ->setMethods(['create'])
            ->getMock();

        $poolFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($servers)
            ->willReturn($poolMock);

        $beanie = Beanie::pool($servers, $poolFactoryMock);


        $this->assertInstanceOf(Beanie::class, $beanie);
    }

    public function testWorker_noServerName_retrievesRandomServer()
    {
        $serverMock = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRandomServer'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('getRandomServer')
            ->willReturn($serverMock);


        $beanie = new Beanie($poolMock);


        $this->assertInstanceOf(Worker::class, $beanie->worker());
    }

    public function testWorker_withName_retrievesServer()
    {
        $serverName = 'test';

        $serverMock = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServer'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('getServer')
            ->with($serverName)
            ->willReturn($serverMock);


        $beanie = new Beanie($poolMock);


        $this->assertInstanceOf(Worker::class, $beanie->worker($serverName));
    }

    public function testProducer_producesProducer()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();


        $beanie = new Beanie($poolMock);
        $producer = $beanie->producer();


        $this->assertInstanceOf(Producer::class, $producer);
    }

    public function testManager_retrievesServer_getsManager()
    {
        $serverName = 'test';

        $managerMock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serverMock = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['getManager'])
            ->getMock();

        $serverMock
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($managerMock);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServer'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('getServer')
            ->willReturn($serverMock);


        $beanie = new Beanie($poolMock);
        $manager = $beanie->manager($serverName);


        $this->assertSame($managerMock, $manager);
    }

    public function testManagers_retrievesServers_getsManagers()
    {
        $managerMock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serverMock = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['getManager'])
            ->getMock();

        $serverMock
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($managerMock);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServers'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('getServers')
            ->willReturn([$serverMock]);


        $beanie = new Beanie($poolMock);
        $managers = $beanie->managers();


        $this->assertSame([$managerMock], $managers);
    }

    public function testWorkers_retrievesServers_getsWorkers()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Server $serverMock */
        $serverMock = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Pool $poolMock */
        $poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServers'])
            ->getMock();

        $poolMock
            ->expects($this->once())
            ->method('getServers')
            ->willReturn([$serverMock]);


        $beanie = new Beanie($poolMock);
        $workers = $beanie->workers();


        $this->assertEquals([new Worker($serverMock)], $workers);
    }
}
