<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class WatchCommandTest extends WithServerMock_TestCase
{
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
}
