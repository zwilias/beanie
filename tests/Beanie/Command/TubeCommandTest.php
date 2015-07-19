<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class TubeCommandTest extends WithServerMock_TestCase
{
    /**
     * @dataProvider validNamesProvider
     * @param string $validName
     */
    public function testConstruct_validName_throwsNoExceptions($validName)
    {
        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $tubeCommandMock->__construct($validName);
        $this->assertInstanceOf('Beanie\Command\TubeCommand', $tubeCommandMock);
    }

    /**
     * @param mixed $invalidName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testConstruct_invalidName_throwsInvalidNameException($invalidName)
    {
        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $tubeCommandMock->__construct($invalidName);
    }

    public function testGetCommandLine_matchesExpectedFormat()
    {
        $testCommandName = 'TESTCOMMAND';
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', $testCommandName, $tubeName);

        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->setConstructorArgs([$tubeName])
            ->setMethods(['_getCommandName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('_getCommandName')
            ->willReturn($testCommandName);


        $commandLine = $tubeCommandMock->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testParseResponse_returnsResponse()
    {
        $testResponseName = 'TESTRESPONSE';
        $testResponseData = 'TESTDATA';

        $responseLine = sprintf('%s %s', $testResponseName, $testResponseData);

        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['_getExpectedResponseName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('_getExpectedResponseName')
            ->willReturn($testResponseName);


        $response = $tubeCommandMock->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals($testResponseName, $response->getName());
        $this->assertEquals($testResponseData, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsUnexpectedResponseException()
    {
        $testExpectedResponseName = 'TESTRESPONSE';
        $testActualResponseName = 'UNEXPECTED';
        $testResponseData = 'TESTDATA';

        $responseLine = sprintf('%s %s', $testExpectedResponseName, $testResponseData);

        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['_getExpectedResponseName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('_getExpectedResponseName')
            ->willReturn($testActualResponseName);


        $tubeCommandMock->parseResponse($responseLine, $this->_getServerMock());
    }

    public function testHasData_noData()
    {
        /** @var TubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\TubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['_getExpectedResponseName'])
            ->getMockForAbstractClass();


        $this->assertFalse($tubeCommandMock->hasData());
    }

    public function validNamesProvider()
    {
        return [
            ['default'],
            ['(why-_such'],
            ['_N4me)s$'],
            ['A-Za-z0-9+/;.$_()-'],
            [str_repeat('a', 200)]
        ];
    }

    public function invalidNamesProvider()
    {
        return [
            [true],
            [10],
            [''],
            [new \stdClass()],
            ['contains spaces'],
            ['-startsWithHyphen'],
            ['contains@illegalCharacter'],
            [str_repeat('a', 201)]
        ];
    }
}
