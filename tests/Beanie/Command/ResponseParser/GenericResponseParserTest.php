<?php


namespace Beanie\Command\ResponseParser;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

use Beanie\Command\Response;
use Beanie\Command\WithServerMock_TestCase;
use Beanie\Exception\DrainingException;

class GenericResponseParserTest extends WithServerMock_TestCase
{
    /**
     * @expectedException \Beanie\Exception\DrainingException
     */
    public function testParseResponse_expectedErrorResponse_throwsException()
    {
        $expectedErrorResponse = [
            Response::FAILURE_DRAINING => DrainingException::class
        ];

        $responseParser = new GenericResponseParser([], $expectedErrorResponse);


        $responseParser->parseResponse(Response::FAILURE_DRAINING, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     */
    public function testParseResponse_unexpectedResponse_throwsException()
    {
        $acceptableResponses = [
            Response::RESPONSE_OK
        ];

        $responseParser = new GenericResponseParser($acceptableResponses, []);


        $responseParser->parseResponse(Response::RESPONSE_FOUND, $this->_getServerMock());
    }

    public function testParseResponse_returnsResponse()
    {
        $acceptableResponses = [
            Response::RESPONSE_OK
        ];

        $serverMock = $this->_getServerMock();

        $responseParser = new GenericResponseParser($acceptableResponses, []);


        $response = $responseParser->parseResponse(Response::RESPONSE_OK, $serverMock);


        $this->assertInstanceOf(Response::class, $response);
        $this->assertNull($response->getData());
        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertSame($serverMock, $response->getServer());
    }
}
