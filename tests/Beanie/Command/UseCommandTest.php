<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class UseCommandTest extends WithServerMock_TestCase
{
    /**
     * @dataProvider validNamesProvider
     * @param string $validName
     */
    public function testConstruct_validName_createsUseCommand($validName)
    {
        $useCommand = new UseCommand($validName);


        $this->assertInstanceOf('Beanie\Command\UseCommand', $useCommand);
    }

    /**
     * @param mixed $invalidName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testConstruct_invalidName_throwsInvalidNameException($invalidName)
    {
        new UseCommand($invalidName);
    }

    public function testGetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_USE, $tubeName);


        $commandLine = (new UseCommand($tubeName))->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_USING, Beanie::DEFAULT_TUBE);
        $useCommand = new UseCommand(Beanie::DEFAULT_TUBE);


        $response = $useCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_USING, $response->getName());
        $this->assertEquals(Beanie::DEFAULT_TUBE, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsUnexpectedResponseException()
    {
        $responseLine = sprintf('%s %s', 'unexpected', Beanie::DEFAULT_TUBE);
        $useCommand = new UseCommand(Beanie::DEFAULT_TUBE);


        $useCommand->parseResponse($responseLine, $this->_getServerMock());
    }

    public function testHasData_noData()
    {
        $useCommand = new UseCommand(Beanie::DEFAULT_TUBE);


        $this->assertFalse($useCommand->hasData());
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
