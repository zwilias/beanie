<?php


namespace Beanie\Command;

use Beanie\Beanie;
use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;

require_once 'WithServerMock_TestCase.php';

class ReleaseCommandTest extends WithServerMock_TestCase
{
    const TEST_ID = 123;

    public function testGetCommandLine_noArgs_defaultValues()
    {
        $expected = join(' ', [
            Command::COMMAND_RELEASE,
            self::TEST_ID,
            Beanie::DEFAULT_PRIORITY,
            Beanie::DEFAULT_DELAY
        ]);


        $releaseCommand = new ReleaseCommand(self::TEST_ID);


        $this->assertEquals($expected, $releaseCommand->getCommandLine());
    }

    public function testGetCommandLine_args_usesArgs()
    {
        $priority = 5;
        $delay = 6;

        $expected = join(' ', [
            Command::COMMAND_RELEASE,
            self::TEST_ID,
            $priority,
            $delay
        ]);


        $releaseCommand = new ReleaseCommand(self::TEST_ID, $priority, $delay);


        $this->assertEquals($expected, $releaseCommand->getCommandLine());
    }

    /**
     * @param $response
     * @param $exceptionClass
     * @param $exceptionCode
     *
     * @dataProvider failureResponses
     */
    public function testParseResponse_responseFailure_throwsAppropriateException
    (
        $response,
        $exceptionClass,
        $exceptionCode
    )
    {
        $releaseCommand = new ReleaseCommand(self::TEST_ID);
        $caughtException = false;

        try {
            $releaseCommand->parseResponse($response, $this->_getServerMock());
        } catch (\Exception $exception) {
            $caughtException = true;

            $this->assertInstanceOf($exceptionClass, $exception);
            $this->assertEquals($exceptionCode, $exception->getCode());
        }

        if (!$caughtException) {
            $this->fail('Expected exception of type ' . $exceptionClass);
        }
    }

    /**
     * @param $responseName
     *
     * @dataProvider successResponses
     */
    public function testParseResponse_responseSuccess($responseName)
    {
        $releaseCommand = new ReleaseCommand(self::TEST_ID);


        $response = $releaseCommand->parseResponse($responseName, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Response', $response);
        $this->assertEquals($responseName, $response->getName());
        $this->assertNull($response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    public function failureResponses()
    {
        return [
            'not found' => [
                Response::FAILURE_NOT_FOUND,
                '\Beanie\Exception\NotFoundException',
                NotFoundException::DEFAULT_CODE
            ],
            'unexpected' => [
                'WHAT',
                '\Beanie\Exception\UnexpectedResponseException',
                UnexpectedResponseException::DEFAULT_CODE
            ]
        ];
    }

    public function successResponses()
    {
        return [
            'release' => [
                Response::RESPONSE_RELEASED
            ],
            'buried' => [
                Response::RESPONSE_BURIED
            ]
        ];
    }
}
