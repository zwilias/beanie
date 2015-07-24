<?php


namespace Beanie\Command;

use Beanie\Beanie;


require_once 'WithServerMock_TestCase.php';

class BuryCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 999;

    public function testGetCommandLine_noArgs_usesDefault()
    {
        $buryCommand = new BuryCommand(self::TEST_ID);
        $expected = join(' ', [
            Command::COMMAND_BURY,
            self::TEST_ID,
            Beanie::DEFAULT_PRIORITY
        ]);


        $this->assertEquals($expected, $buryCommand->getCommandLine());
    }

    public function testGetCommandLine_withArgs_usesArgs()
    {
        $priority = 8888;
        $expected = join(' ', [
            Command::COMMAND_BURY,
            self::TEST_ID,
            $priority
        ]);

        $buryCommand = new BuryCommand(self::TEST_ID, $priority);


        $this->assertEquals($expected, $buryCommand->getCommandLine());
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseResponse_notFoundFailure_throwsException()
    {
        (new BuryCommand(self::TEST_ID))->parseResponse(Response::FAILURE_NOT_FOUND, $this->_getServerMock());
    }

    /**
     * @expectedException \Beanie\Exception\UnexpectedResponseException
     * @expectedException 402
     */
    public function testParseResponse_unexpectedResponse_throwsException()
    {
        (new BuryCommand(self::TEST_ID))->parseResponse('WTF', $this->_getServerMock());
    }

    public function testParseResponse_buriedResponse_returnsResponse()
    {
        $response = (new BuryCommand(self::TEST_ID))->parseResponse(Response::RESPONSE_BURIED, $this->_getServerMock());


        $this->assertInstanceOf('\Beanie\Command\Response', $response);
        $this->assertEquals(Response::RESPONSE_BURIED, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }
}
