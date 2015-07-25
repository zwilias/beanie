<?php


namespace Beanie\Command;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

use Beanie\Command\CommandLineCreator\CommandLineCreator;
use Beanie\Command\ResponseParser\ResponseParser;
use Beanie\WithServerMock_TestCase;

class GenericCommandTest extends WithServerMock_TestCase
{
    public function testGenericCommand_actsAsFacade()
    {
        $serverMock = $this->getServerMock();
        $response = new Response('test', null, $serverMock);

        /** @var \PHPUnit_Framework_MockObject_MockObject|CommandLineCreator $commandLineCreatorMock */
        $commandLineCreatorMock = $this
            ->getMockBuilder(CommandLineCreator::class)
            ->setMethods(['getCommandLine', 'hasData', 'getData'])
            ->getMockForAbstractClass()
        ;

        $commandLineCreatorMock
            ->expects($this->once())
            ->method('getCommandLine')
            ->willReturn('commandline')
        ;

        $commandLineCreatorMock
            ->expects($this->once())
            ->method('hasData')
            ->willReturn(false)
        ;

        $commandLineCreatorMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn('data')
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseParser $responseParserMock */
        $responseParserMock = $this
            ->getMockBuilder(ResponseParser::class)
            ->setMethods(['parseResponse'])
            ->getMockForAbstractClass()
        ;

        $responseParserMock
            ->expects($this->once())
            ->method('parseResponse')
            ->with('responseline', $serverMock)
            ->willReturn($response)
        ;


        $genericCommand = new GenericCommand($commandLineCreatorMock, $responseParserMock);


        $this->assertEquals('commandline', $genericCommand->getCommandLine());
        $this->assertFalse($genericCommand->hasData());
        $this->assertEquals('data', $genericCommand->getData());
        $this->assertSame($response, $genericCommand->parseResponse('responseline', $serverMock));
    }
}
