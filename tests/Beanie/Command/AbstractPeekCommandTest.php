<?php


namespace Beanie\Command;

use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class AbstractPeekCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 567;

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\AbstractPeekCommand $command */
        $command = $this->getMockBuilder('\Beanie\Command\AbstractPeekCommand')->getMockForAbstractClass();
        $command->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
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

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\AbstractPeekCommand $command */
        $command = $this->getMockBuilder('\Beanie\Command\AbstractPeekCommand')->getMockForAbstractClass();


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
