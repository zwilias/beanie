<?php


namespace Beanie\Command;

use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class DeleteCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 5;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_DELETE, self::TEST_ID);


        $deleteCommand = new DeleteCommand(self::TEST_ID);


        $this->assertEquals($expected, $deleteCommand->getCommandLine());
    }

    public function testParseResponse_deleted_returnsResponseWithoutData()
    {
        $deleteCommand = new DeleteCommand(self::TEST_ID);


        $response = $deleteCommand->parseResponse(Response::RESPONSE_DELETED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Response', $response);
        $this->assertEquals(Response::RESPONSE_DELETED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsException()
    {
        (new DeleteCommand(self::TEST_ID))->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new DeleteCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }
}
