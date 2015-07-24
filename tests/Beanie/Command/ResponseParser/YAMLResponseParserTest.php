<?php


namespace Beanie\Command\ResponseParser;

require_once __DIR__ . '/../WithServerMock_TestCase.php';

use Beanie\Command\WithServerMock_TestCase;
use Beanie\Exception\Exception;
use Symfony\Component\Yaml\Yaml;

class YAMLResponseParserTest extends WithServerMock_TestCase
{
    public function testParseResponse_parsesYAML()
    {
        $dataArray = [
            'some' => 'data',
            'is' => 'here'
        ];

        $dataYAML = Yaml::dump($dataArray);

        $responseLine = sprintf('%s %s', 'test', strlen($dataYAML));

        $serverMock = $this->_getServerReturningYAMLData($dataYAML);


        $responseParser = new YAMLResponseParser(['test']);
        $response = $responseParser->parseResponse($responseLine, $serverMock);


        $this->assertEquals($dataArray, $response->getData());
    }

    public function testParseResponse_invalidYAML_throwsWrappedParseException()
    {
        $gotException = false;
        $invalidYAMLData = '[YAML can\'t handle this';

        $responseLine = sprintf('%s %s', 'test', strlen($invalidYAMLData));
        $serverMock = $this->_getServerReturningYAMLData($invalidYAMLData);


        $responseParser = new YAMLResponseParser(['test']);


        try {
            $responseParser->parseResponse($responseLine, $serverMock);
        } catch (Exception $exception) {
            $this->assertInstanceOf('\Symfony\Component\Yaml\Exception\ParseException', $exception->getPrevious());
            $gotException = true;
        }

        if (!$gotException) {
            $this->fail('Excepted wrapped ParseException');
        }
    }
}
