<?php


namespace Beanie\Command;

use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class PeekCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 567;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_PEEK, self::TEST_ID);


        $this->assertEquals($expected, (new PeekCommand(self::TEST_ID))->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new PeekCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    public function testParseResponse_foundResponse_returnsResponse()
    {
        $data = 'this is some testdata and might as well be an actual job';

        $serverMock = $this->_getServerMock();
        $serverMock->expects($this->once())
            ->method('getData')
            ->with(strlen($data))
            ->willReturn($data);

        $responseLine = join(' ', [
            Response::RESPONSE_FOUND,
            self::TEST_ID,
            strlen($data)
        ]);

        $command = new PeekCommand(self::TEST_ID);


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertInstanceOf('\Beanie\Response', $response);
        $this->assertEquals(Response::RESPONSE_FOUND, $response->getName());
        $this->assertEquals($serverMock, $response->getServer());
        $this->assertEquals([
            'id' => self::TEST_ID,
            'data' => $data
        ], $response->getData());
    }
}
