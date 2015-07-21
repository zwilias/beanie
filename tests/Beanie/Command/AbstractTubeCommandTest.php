<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;

require_once 'WithServerMock_TestCase.php';

class AbstractTubeCommandTest extends WithServerMock_TestCase
{
    /**
     * @dataProvider validNamesProvider
     * @param string $validName
     */
    public function testConstruct_validName_throwsNoExceptions($validName)
    {
        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $tubeCommandMock->__construct($validName);
        $this->assertInstanceOf('Beanie\Command\AbstractTubeCommand', $tubeCommandMock);
    }

    /**
     * @param mixed $invalidName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testConstruct_invalidName_throwsInvalidNameException($invalidName)
    {
        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $tubeCommandMock->__construct($invalidName);
    }

    public function testGetCommandLine_matchesExpectedFormat()
    {
        $testCommandName = 'TESTCOMMAND';
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', $testCommandName, $tubeName);

        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->setConstructorArgs([$tubeName])
            ->setMethods(['getCommandName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('getCommandName')
            ->willReturn($testCommandName);


        $commandLine = $tubeCommandMock->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testParseResponse_returnsResponse()
    {
        $testResponseName = 'TESTRESPONSE';
        $testResponseData = 'TESTDATA';

        $responseLine = sprintf('%s %s', $testResponseName, $testResponseData);

        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['getExpectedResponseName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('getExpectedResponseName')
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

        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['getExpectedResponseName'])
            ->getMockForAbstractClass();

        $tubeCommandMock
            ->expects($this->once())
            ->method('getExpectedResponseName')
            ->willReturn($testActualResponseName);


        $tubeCommandMock->parseResponse($responseLine, $this->_getServerMock());
    }

    public function testHasData_noData()
    {
        /** @var AbstractTubeCommand|\PHPUnit_Framework_MockObject_MockObject $tubeCommandMock */
        $tubeCommandMock = $this
            ->getMockBuilder('Beanie\Command\AbstractTubeCommand')
            ->setConstructorArgs([Beanie::DEFAULT_TUBE])
            ->setMethods(['getExpectedResponseName'])
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
