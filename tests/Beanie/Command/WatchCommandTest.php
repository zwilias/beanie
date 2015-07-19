<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class WatchCommandTest extends WithServerMock_TestCase
{
    /**
     * @dataProvider validNamesProvider
     * @param string $validName
     */
    public function testConstruct_validName_createsWatchCommand($validName)
    {
        $watchCommand = new WatchCommand($validName);


        $this->assertInstanceOf('Beanie\Command\WatchCommand', $watchCommand);
    }

    /**
     * @param mixed $invalidName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testConstruct_invalidName_throwsInvalidNameException($invalidName)
    {
        new WatchCommand($invalidName);
    }

    public function testGetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_WATCH, $tubeName);


        $commandLine = (new WatchCommand($tubeName))->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_WATCHING, 1);
        $watchCommand = new WatchCommand(Beanie::DEFAULT_TUBE);


        $response = $watchCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_WATCHING, $response->getName());
        $this->assertEquals(1, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsUnexpectedResponseException()
    {
        $responseLine = sprintf('%s %s', 'unexpected', 1);
        $watchCommand = new WatchCommand(Beanie::DEFAULT_TUBE);


        $watchCommand->parseResponse($responseLine, $this->_getServerMock());
    }

    public function testHasData_noData()
    {
        $watchCommand = new WatchCommand(Beanie::DEFAULT_TUBE);


        $this->assertFalse($watchCommand->hasData());
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
