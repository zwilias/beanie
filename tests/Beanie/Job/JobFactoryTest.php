<?php


namespace Beanie\Job;



use Beanie\Command\Response;
use Beanie\Server\Server;

class JobFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected static $responseToStateMap = [
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


        $job = $JobFactory->createFrom($response);


        $this->assertEquals($expectedState, $job->getState());
        $this->assertEquals(self::TEST_ID, $job->getId());
        $this->assertEquals(self::TEST_DATA, $job->getData());
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


        $JobFactory->createFrom($response);
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


        $JobFactory->createFrom($response);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server
     */
    public function getServerMock()
    {
        return $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
