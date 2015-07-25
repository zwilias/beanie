<?php


namespace Beanie\Command\ResponseParser;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

use Beanie\Command\Response;
use Beanie\Command\WithServerMock_TestCase;

class JobResponseParserTest extends WithServerMock_TestCase
{
    public function testParseResponse_responseContainsJobData()
    {
        $jobData = 'data';
        $jobId = 12;
        $responseName = Response::RESPONSE_RESERVED;
        $responseLine = sprintf('%s %s %s', $responseName, $jobId, strlen($jobData));

        $serverMock = $this->_getServerMock();
        $serverMock
            ->expects($this->once())
            ->method('readData')
            ->with(strlen($jobData))
            ->willReturn($jobData)
        ;


        $parser = new JobResponseParser([$responseName]);
        $response = $parser->parseResponse($responseLine, $serverMock);


        $this->assertEquals([
            'id' => $jobId,
            'data' => $jobData
        ], $response->getData());
        $this->assertEquals($responseName, $response->getName());
    }
}
