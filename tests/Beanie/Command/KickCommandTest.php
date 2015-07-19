<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class KickCommandTest extends WithServerMock_TestCase
{
    public function testGetCommandLine_noArgs_usesDefaults()
    {
        $expected = sprintf('%s %s', Command::COMMAND_KICK, Beanie::DEFAULT_MAX_TO_KICK);


        $command = new KickCommand();


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testGetCommandLine_withArgs_usesArgs()
    {
        $maxToKick = 10;
        $expected = sprintf('%s %s', Command::COMMAND_KICK, $maxToKick);


        $command = new KickCommand($maxToKick);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testParseResponse_kicked_returnsResponse()
    {
        $kicked = 10;
        $responseLine = sprintf('%s %s', Response::RESPONSE_KICKED, $kicked);


        $command = new KickCommand();
        $response = $command->parseResponse($responseLine, $this->_getServerMock());


        $this->assertInstanceOf('\Beanie\Response', $response);
        $this->assertEquals(Response::RESPONSE_KICKED, $response->getName());
        $this->assertEquals($kicked, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }
}
