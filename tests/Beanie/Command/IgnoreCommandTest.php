<?php


namespace Beanie\Command;

use Beanie\Beanie;


require_once 'WithServerMock_TestCase.php';

class IgnoreCommandTest extends WithServerMock_TestCase
{
    public function testGetCommandLine_matchesExpectedFormat()
    {
        $tubeName = Beanie::DEFAULT_TUBE;
        $expected = sprintf('%s %s', Command::COMMAND_IGNORE, $tubeName);


        $commandLine = (new IgnoreCommand($tubeName))->getCommandLine();


        $this->assertEquals($expected, $commandLine);
    }

    public function testParseResponse_returnsResponse()
    {
        $responseLine = sprintf('%s %s', Response::RESPONSE_WATCHING, Beanie::DEFAULT_TUBE);
        $ignoreCommand = new IgnoreCommand(Beanie::DEFAULT_TUBE);


        $response = $ignoreCommand->parseResponse($responseLine, $this->_getServerMock());


        $this->assertEquals(Response::RESPONSE_WATCHING, $response->getName());
        $this->assertEquals(Beanie::DEFAULT_TUBE, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\NotIgnoredException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notIgnore_throwsNotIgnoreException()
    {
        $ignoreCommand = new IgnoreCommand(Beanie::DEFAULT_TUBE);


        $ignoreCommand->parseResponse(Response::FAILURE_NOT_IGNORED, $this->_getServerMock());
    }
}
