<?php


namespace Beanie\Command;

require_once 'WithServerMock_TestCase.php';

use Beanie\Beanie;

use Beanie\Exception\DrainingException;
use Beanie\Exception\ExpectedCRLFException;
use Beanie\Exception\JobTooBigException;


class PutCommandTest extends WithServerMock_TestCase
{
    const TEST_DATA = 'testdata';

    public function testConstruct_withData_containsData()
    {
        $putCommand = new PutCommand(self::TEST_DATA);


        $this->assertTrue($putCommand->hasData());
        $this->assertEquals(self::TEST_DATA, $putCommand->getData());
    }

    public function testGetCommandLine_noArgs_defaultValues()
    {
        $expected = join(' ', [
            Command::COMMAND_PUT,
            Beanie::DEFAULT_PRIORITY,
            Beanie::DEFAULT_DELAY,
            Beanie::DEFAULT_TIME_TO_RUN,
            strlen(self::TEST_DATA)
        ]);


        $putCommand = new PutCommand(self::TEST_DATA);


        $this->assertEquals($expected, $putCommand->getCommandLine());
    }

    public function testGetCommandLine_args_usesArgs()
    {
        $priority = 5;
        $delay = 6;
        $timeToRun = 7;

        $expected = join(' ', [
            Command::COMMAND_PUT,
            $priority,
            $delay,
            $timeToRun,
            strlen(self::TEST_DATA)
        ]);


        $putCommand = new PutCommand(self::TEST_DATA, $priority, $delay, $timeToRun);


        $this->assertEquals($expected, $putCommand->getCommandLine());
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
        $putCommand = new PutCommand(self::TEST_DATA);
        $caughtException = false;

        try {
            $putCommand->parseResponse($response, $this->_getServerMock());
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
     * @param $response
     * @param $type
     * @param $data
     *
     * @dataProvider successResponses
     */
    public function testParseResponse_responseSuccess($response, $type, $data)
    {
        $putCommand = new PutCommand(self::TEST_DATA);


        $response = $putCommand->parseResponse($response, $this->_getServerMock());


        $this->assertInstanceOf('Beanie\Command\Response', $response);
        $this->assertEquals($type, $response->getName());
        $this->assertEquals($data, $response->getData());
        $this->assertEquals($this->_getServerMock(), $response->getServer());
    }

    public function failureResponses()
    {
        return [
            'draining' => [
                Response::FAILURE_DRAINING,
                '\Beanie\Exception\DrainingException',
                DrainingException::DEFAULT_CODE
            ],
            'job too big' => [
                Response::FAILURE_JOB_TOO_BIG,
                '\Beanie\Exception\JobTooBigEXception',
                JobTooBigException::DEFAULT_CODE
            ],
            'expected crlf' => [
                Response::FAILURE_EXPECTED_CRLF,
                '\Beanie\Exception\ExpectedCRLFException',
                ExpectedCRLFException::DEFAULT_CODE
            ]
        ];
    }

    public function successResponses()
    {
        return [
            'inserted' => [
                Response::RESPONSE_INSERTED . ' 1',
                Response::RESPONSE_INSERTED,
                1
            ],
            'buried' => [
                Response::RESPONSE_BURIED . ' 2',
                Response::RESPONSE_BURIED,
                2
            ]
        ];
    }
}
