<?php


namespace Beanie\Job;

require_once __DIR__ . '/../WithServerMock_TestCase.php';


use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Command\Response;
use Beanie\WithServerMock_TestCase;

class JobFactoryTest extends WithServerMock_TestCase
{
    protected static $responseToStateMap = [
        Response::RESPONSE_RESERVED => Job::STATE_RESERVED,
        Response::RESPONSE_INSERTED => Job::STATE_RELEASED,
        Response::RESPONSE_RELEASED => Job::STATE_RELEASED,
        Response::RESPONSE_BURIED => Job::STATE_BURIED,
        Response::RESPONSE_OK => Job::STATE_UNKNOWN
    ];

    const TEST_ID = 123;
    const TEST_DATA = 'test';

    /**
     * @param string $responseName
     * @param string $expectedState
     *
     * @dataProvider correctJobDataProvider
     */
    public function testCreate_withCorrectData_createsJob($responseName, $expectedState)
    {
        $response = new Response($responseName, [
            'id' => self::TEST_ID,
            'data' => self::TEST_DATA
        ], $this->getServerMock());

        $JobFactory = new JobFactory();


        $job = $JobFactory->createFromResponse($response);


        $this->assertEquals($expectedState, $job->getState());
        $this->assertEquals(self::TEST_ID, $job->getId());
        $this->assertEquals(self::TEST_DATA, $job->getData());
    }

    public function testCreateFromCommand_createsJob()
    {
        $serverMock = $this->getServerMock(['dispatchCommand']);

        $command = CommandFactory::instance()->create(Command::COMMAND_PEEK, [self::TEST_ID]);

        $response = new Response(Response::RESPONSE_FOUND, [
            'id' => self::TEST_ID,
            'data' => self::TEST_DATA
        ], $serverMock);

        $serverMock
            ->expects($this->once())
            ->method('dispatchCommand')
            ->with($this->callback(function (Command $command) {
                return $command->getCommandLine() == sprintf('%s %s', Command::COMMAND_PEEK, self::TEST_ID);
            }))
            ->willReturn($this->oath($response));

        $JobFactory = new JobFactory();


        $job = $JobFactory->createFromCommand($command, $serverMock)->invoke();


        $this->assertEquals(Job::STATE_UNKNOWN, $job->getState());
        $this->assertEquals(self::TEST_ID, $job->getId());
        $this->assertEquals(self::TEST_DATA, $job->getData());
    }

    public function testUnsetInstance_unsetsInstance()
    {
        $instance = JobFactory::instance();
        JobFactory::unsetInstance();

        $this->assertNotSame($instance, JobFactory::instance());
    }

    public function testInstance_consecutiveCalls_returnSame()
    {
        $instance = JobFactory::instance();


        $this->assertSame($instance, JobFactory::instance());
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testCreate_noJobId_throwsInvalidArgumentException()
    {
        $response = new Response(Response::RESPONSE_OK, [
            'data' => self::TEST_DATA
        ], $this->getServerMock());

        $JobFactory = new JobFactory();


        $JobFactory->createFromResponse($response);
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testCreate_noJobData_throwsInvalidArgumentException()
    {
        $response = new Response(Response::RESPONSE_OK, [
            'id' => self::TEST_ID
        ], $this->getServerMock());

        $JobFactory = new JobFactory();


        $JobFactory->createFromResponse($response);
    }
    /**
     * @return array
     */
    public function correctJobDataProvider()
    {
        $result = [];

        foreach (self::$responseToStateMap as $response => $expectedState) {
            $result[] = [$response, $expectedState];
        }

        return $result;
    }
}
