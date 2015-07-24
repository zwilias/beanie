<?php


namespace Beanie\Command;


require_once 'WithServerMock_TestCase.php';

class KickJobCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 5;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_KICK_JOB, self::TEST_ID);


        $kickCommand = new KickJobCommand(self::TEST_ID);


        $this->assertEquals($expected, $kickCommand->getCommandLine());
    }

    public function testParseResponse_deleted_returnsResponseWithoutData()
    {
        $kickCommand = new KickJobCommand(self::TEST_ID);


        $response = $kickCommand->parseResponse(Response::RESPONSE_KICKED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_KICKED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsException()
    {
        (new KickJobCommand(self::TEST_ID))->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new KickJobCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }
}
