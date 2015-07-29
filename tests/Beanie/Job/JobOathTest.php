<?php


namespace Beanie\Job;


use Beanie\Server\ResponseOath;

class JobOathTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSocket_asksInternalResponseOath()
    {
        $internalSocket = 'socket';

        /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseOath $responseOathMock */
        $responseOathMock = $this->getMockBuilder(ResponseOath::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSocket'])
            ->getMock();

        $responseOathMock
            ->expects($this->once())
            ->method('getSocket')
            ->willReturn($internalSocket);

        $jobOath = new JobOath($responseOathMock);


        $this->assertSame($internalSocket, $jobOath->getSocket());
    }
}
