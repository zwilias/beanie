<?php


namespace Beanie\Command;

require_once 'WithServerMock_TestCase.php';

use Beanie\Exception\Exception;

use Symfony\Component\Yaml\Yaml;

class AbstractWithYAMLResponseCommandTest extends WithServerMock_TestCase
{
    public function testParseResponse_validYAML_returnsOKResponse()
    {
        $testDataRaw = [
            'this' => 1,
            'is' => 2,
            'some' => 3,
            'testing' => 4,
            'data' => 5
        ];
        $testYAMLData = Yaml::dump($testDataRaw);

        $serverMock = $this->_getServerReturningYAMLData($testYAMLData);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\AbstractWithYAMLResponseCommand $command */
        $command = $this->getMockBuilder('\Beanie\Command\AbstractWithYAMLResponseCommand')->getMockForAbstractClass();

        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($testYAMLData));


        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertInstanceOf('\Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertEquals($serverMock, $response->getServer());
        $this->assertEquals($testDataRaw, $response->getData());
    }

    public function testParseResponse_invalidYAML_throwsWrappedParseException()
    {
        $gotException = false;

        $invalidYAML = "\tYAML can't contain tabs as indentation";
        $serverMock = $this->_getServerReturningYAMLData($invalidYAML);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Beanie\Command\AbstractWithYAMLResponseCommand $command */
        $command = $this->getMockBuilder('\Beanie\Command\AbstractWithYAMLResponseCommand')->getMockForAbstractClass();

        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($invalidYAML));


        try {
            $command->parseResponse($responseLine, $serverMock);
        } catch (Exception $exception) {
            $this->assertInstanceOf('\Beanie\Exception\Exception', $exception);
            $previous = $exception->getPrevious();

            $this->assertInstanceOf('\Symfony\Component\Yaml\Exception\ParseException', $previous);
            $gotException = true;
        }

        if (! $gotException) {
            $this->fail('Expected wrapped ParseException to be thrown');
        }
    }
}
