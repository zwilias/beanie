<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class PauseTubeCommandTest extends WithServerMock_TestCase
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
        new PauseTubeCommand($tubeName);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testConstruct_validTubeName_noException($tubeName)
    {
        $command = new PauseTubeCommand($tubeName);


        $this->assertInstanceOf('\Beanie\Command\PauseTubeCommand', $command);
    }

    /**
     * @param string $tubeName
     * @dataProvider validNamesProvider
     */
    public function testGetCommandLine_noArgs_usesDefaults($tubeName)
    {
        $expected = join(' ', [Command::COMMAND_PAUSE_TUBE, $tubeName, Beanie::DEFAULT_DELAY]);
        $command = new PauseTubeCommand($tubeName);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    public function testGetCommandLine_args_usesArgs()
    {
        $delay = 123;
        $expected = join(' ', [Command::COMMAND_PAUSE_TUBE, self::TEST_TUBE, $delay]);


        $command = new PauseTubeCommand(self::TEST_TUBE, $delay);


        $this->assertEquals($expected, $command->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsNotFoundException()
    {
        (new PauseTubeCommand(self::TEST_TUBE))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedExceptionCode 402
     */
    public function testParseResponse_unexpectedResponse_throwsUnexpectedResponseException()
    {
        (new PauseTubeCommand(self::TEST_TUBE))->parseResponse('something', $this->_getServerMock());
    }

    public function testParseResponse_pausedResponse_returnsResponse()
    {
        $response = (new PauseTubeCommand(self::TEST_TUBE))->parseResponse(Response::RESPONSE_PAUSED, $this->_getServerMock());


        $this->assertInstanceOf('\Beanie\Response', $response);
        $this->assertEquals(Response::RESPONSE_PAUSED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
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
