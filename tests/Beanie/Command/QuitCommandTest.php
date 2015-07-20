<?php


namespace Beanie\Command;

use Beanie\Command;

require_once 'WithServerMock_TestCase.php';

class QuitCommandTest extends WithServerMock_TestCase
{
    public function testGetCommandLine_correctCommand()
    {
        $this->assertEquals(Command::COMMAND_QUIT, (new QuitCommand())->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_noResponseExpected_throwsUnexpectedResponse()
    {
        (new QuitCommand())->parseResponse('something', $this->_getServerMock());
    }
}
