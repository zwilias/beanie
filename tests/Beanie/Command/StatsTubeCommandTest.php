<?php


namespace Beanie\Command;


use Symfony\Component\Yaml\Yaml;

require_once 'WithServerMock_TestCase.php';

class StatsTubeCommandTest extends WithServerMock_TestCase
{
    const TEST_TUBE = 'tube';

    /**
     * @param mixed $tubeName
     * @dataProvider invalidNamesProvider
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     */
    public function testConstruct_invalidTubeName_ThrowsInvalidNameException($tubeName)
    {
        new StatsTubeCommand($tubeName);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testConstruct_validTubeName_noException($tubeName)
    {
        $command = new StatsTubeCommand($tubeName);


        $this->assertInstanceOf('\Beanie\Command\StatsTubeCommand', $command);
    }

    public function testGetCommandLine_correctFormat()
    {
        $expected = sprintf('%s %s', Command::COMMAND_STATS_TUBE, self::TEST_TUBE);


        $command = new StatsTubeCommand(self::TEST_TUBE);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new StatsTubeCommand(self::TEST_TUBE))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    public function testParseResponse_OKResponse_callsParent()
    {
        $testData = ['hello'];
        $testDataYAML = Yaml::dump($testData);
        $serverMock = $this->_getServerReturningYAMLData($testDataYAML);
        $responseLine = sprintf('%s %s', Response::RESPONSE_OK, strlen($testDataYAML));


        $command = new StatsTubeCommand(self::TEST_TUBE);
        $response = $command->parseResponse($responseLine, $serverMock);


        $this->assertEquals(Response::RESPONSE_OK, $response->getName());
        $this->assertThat($response->getData(), $this->isType('array'));
    }

    public function validNamesProvider()
    {
        return [
            ['default'],
            ['(why-_such'],
            ['_N4me)s$'],
            ['A-Za-z0-9+/;.$_()-'],
            [str_repeat('a', 200)]
        ];
    }

    public function invalidNamesProvider()
    {
        return [
            [true],
            [10],
            [''],
            [new \stdClass()],
            ['contains spaces'],
            ['-startsWithHyphen'],
            ['contains@illegalCharacter'],
            [str_repeat('a', 201)]
        ];
    }
}
