<?php


namespace Beanie\Command;


class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testParseLine_noKnownErrors_callsChild()
    {
        $responseLine = 'test response';
        $expectedResponse = 'expected';

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server $serverMock */
        $serverMock = $this
            ->getMockBuilder('\Beanie\Server\Server')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this
            ->getMockBuilder('\Beanie\Command\AbstractCommand')
            ->setMethods(['_parseResponse'])
            ->getMockForAbstractClass()
        ;

        $commandMock
            ->expects($this->once())
            ->method('_parseResponse')
            ->with($responseLine, $serverMock)
            ->willReturn($expectedResponse)
        ;


        $response = $commandMock->parseResponse($responseLine, $serverMock);


        $this->assertEquals($expectedResponse, $response);
    }
}
