<?php


namespace Beanie\Command;


require_once 'WithServerMock_TestCase.php';

class TouchCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 5;

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_TOUCH, self::TEST_ID);


        $touchCommand = new TouchCommand(self::TEST_ID);


        $this->assertEquals($expected, $touchCommand->getCommandLine());
    }

    public function testParseResponse_touched_returnsResponseWithoutData()
    {
        $touchCommand = new TouchCommand(self::TEST_ID);


        $response = $touchCommand->parseResponse(Response::RESPONSE_TOUCHED, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_TOUCHED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsException()
    {
        (new TouchCommand(self::TEST_ID))->parseResponse('what', $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new TouchCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }
}
