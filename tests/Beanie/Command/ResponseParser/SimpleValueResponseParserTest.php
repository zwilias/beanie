<?php


namespace Beanie\Command\ResponseParser;

require_once __DIR__ . '/../../WithServerMock_TestCase.php';

use Beanie\Command\Response;
use Beanie\WithServerMock_TestCase;

class SimpleValueResponseParserTest extends WithServerMock_TestCase
{
    public function testParseResponse_containsSimpleValue()
    {
        $simpleValue = 'hello';
        $responseName = Response::RESPONSE_OK;

        $responseLine = sprintf('%s %s', $responseName, $simpleValue);


        $parser = new SimpleValueResponseParser([$responseName]);
        $response = $parser->parseResponse($responseLine, $this->getServerMock());


        $this->assertEquals($responseName, $response->getName());
        $this->assertEquals($simpleValue, $response->getData());
    }
}
