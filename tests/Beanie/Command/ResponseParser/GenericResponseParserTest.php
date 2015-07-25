<?php


namespace Beanie\Command\ResponseParser;

require_once __DIR__ . '/../../WithServerMock_TestCase.php';

use Beanie\Command\Response;
use Beanie\Exception\DrainingException;
use Beanie\WithServerMock_TestCase;

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


        $responseParser->parseResponse(Response::FAILURE_DRAINING, $this->getServerMock());
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


        $responseParser->parseResponse(Response::RESPONSE_FOUND, $this->getServerMock());
    }

    public function testParseResponse_returnsResponse()
    {
        $acceptableResponses = [
            Response::RESPONSE_OK
        ];

        $serverMock = $this->getServerMock();

        $responseParser = new GenericResponseParser($acceptableResponses, []);


        $response = $responseParser->parseResponse(Response::RESPONSE_OK, $serverMock);


        $this->assertInstanceOf(Response::class, $response);
        $this->assertNull($response->getData());
        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertSame($serverMock, $response->getServer());
    }
}
