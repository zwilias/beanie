<?php


namespace Beanie\Command;


require_once 'WithServerMock_TestCase.php';

class ListTubeUsedCommandTest extends WithServerMock_TestCase
{
    public function testGetCommandLine_correctCommandLine()
    {
        $this->assertEquals(Command::COMMAND_LIST_TUBE_USED, (new ListTubeUsedCommand())->getCommandLine());
    }

    public function testParseResponse_returnsResponse_withTubeUsed()
    {
        $tubeUsed = 'some-tube-name';
        $responseLine = sprintf('%s %s', Response::RESPONSE_USING, $tubeUsed);


        $command = new ListTubeUsedCommand();
        $response = $command->parseResponse($responseLine, $this->_getServerMock());


        $this->assertInstanceOf('\Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_USING, $response->getName());
        $this->assertEquals($tubeUsed, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }
}
