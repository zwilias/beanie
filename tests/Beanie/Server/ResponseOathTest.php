<?php


namespace Beanie\Server;

use Beanie\Command\ResponseParser\ResponseParserInterface;
use Beanie\WithServerMock_TestCase;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

class ResponseOathTest extends WithServerMock_TestCase
{
    public function testGetSocket_exposesInternalSocket()
    {
        $internalSocket = 'hi';

        /** @var \PHPUnit_Framework_MockObject_MockObject|Socket $socketMock */
        $socketMock = $this
            ->getMockBuilder(Socket::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRaw'])
            ->getMock();

        $socketMock
            ->expects($this->once())
            ->method('getRaw')
            ->willReturn($internalSocket);

        $serverMock = $this->getServerMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseParserInterface $responseParserMock */
        $responseParserMock = $this
            ->getMockBuilder(ResponseParserInterface::class)
            ->getMockForAbstractClass();

        $responseOath = new ResponseOath($socketMock, $serverMock, $responseParserMock);


        $this->assertSame($internalSocket, $responseOath->getSocket());
    }
}
