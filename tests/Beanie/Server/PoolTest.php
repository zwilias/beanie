<?php


namespace Beanie\Server;


use Beanie\Command\CommandFactory;
use Beanie\Command\CommandInterface;
use Beanie\Tube\TubeStatus;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandFactory */
    protected $commandFactory;

    public function setUp()
    {
        $this->commandFactory = CommandFactory::instance();
    }

    public function testConstruct_withServers()
    {
        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock()
        ;

        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test')
        ;


        $pool = new Pool([$serverMock]);


        $this->assertEquals(['test' => $serverMock], $pool->getServers());
        $this->assertInstanceOf(TubeStatus::class, $pool->getTubeStatus());
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testConstruct_noServers_throwsException()
    {
        new Pool([]);
    }

    public function testGetServer_returnsServer()
    {
        $serverMockBuilder = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
        ;

        $serverMock = $serverMockBuilder->getMock();
        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test')
        ;

        $otherServerMock = $serverMockBuilder->getMock();
        $otherServerMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('other')
        ;


        $pool = new Pool([$serverMock, $otherServerMock]);


        $this->assertEquals($serverMock, $pool->getServer('test'));
        $this->assertEquals($otherServerMock, $pool->getServer('other'));
        $this->assertEquals(['test' => $serverMock, 'other' => $otherServerMock], $pool->getServers());
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testGetServer_unknownName_throwsException()
    {
        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock()
        ;

        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test')
        ;


        $pool = new Pool([$serverMock]);


        $pool->getServer('other');
    }

    public function testDispatchCommand_synchronizesTubeStatusAndDispatches()
    {
        $command = $this->commandFactory->create(CommandInterface::COMMAND_USE, ['test']);
        $testResponse = 'test value';

        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'transformTubeStatusTo', 'dispatchCommand'])
            ->getMock()
        ;

        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test')
        ;

        $serverMock
            ->expects($this->once())
            ->method('transformTubeStatusTo')
            ->willReturnSelf()
        ;

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($command)
            ->willReturn('test value');

        $pool = new Pool([$serverMock]);


        $actualResponse = $pool->dispatchCommand($command);


        $this->assertEquals($testResponse, $actualResponse);
    }

    public function testDispatchCommand_picksRandomServer()
    {
        $command = $this->commandFactory->create(CommandInterface::COMMAND_USE, ['test']);

        $serverMock = $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'transformTubeStatusTo', 'dispatchCommand'])
            ->getMock()
        ;

        $serverMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('test')
        ;

        $serverMock
            ->expects($this->atLeastOnce())
            ->method('transformTubeStatusTo')
            ->willReturnSelf()
        ;

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($command)
            ->willReturn('test value')
        ;


        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Pool $poolStubbedRandom */
        $poolStubbedRandom = $this->getMockBuilder(Pool::class)
            ->setMethods(['getRandomServer'])
            ->setConstructorArgs([[$serverMock]])
            ->getMock()
        ;

        $poolStubbedRandom
            ->expects($this->once())
            ->method('getRandomServer')
            ->willReturn($serverMock)
        ;


        $poolStubbedRandom->dispatchCommand($command);
    }
}
