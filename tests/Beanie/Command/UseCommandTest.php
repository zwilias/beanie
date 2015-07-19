<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class UseCommandTest extends WithServerMock_TestCase
{
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
}
