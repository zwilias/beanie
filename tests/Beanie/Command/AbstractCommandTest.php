<?php


namespace Beanie\Command;


use Beanie\Response;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testParseLine_noKnownErrors_callsChild()
    {
        $responseLine = 'test response';
        $expectedResponse = 'expected';

        $serverMock = $this->_getServerMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this
            ->getMockBuilder('\Beanie\Command\AbstractCommand')
            ->setMethods(['_parseResponse'])
            ->getMockForAbstractClass()
        ;

        $commandMock
            ->expects($this->once())
            ->method('_parseResponse')
            ->with($responseLine, $serverMock)
            ->willReturn($expectedResponse)
        ;


        $response = $commandMock->parseResponse($responseLine, $serverMock);


        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @expectedException \Beanie\Exception\BadFormatException
     * @expectedExceptionCode 400
     */
    public function testParseLine_badFormat_throwsBadFormatException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this->getMockForAbstractClass('\Beanie\Command\AbstractCommand');


        $commandMock->parseResponse(Response::ERROR_BAD_FORMAT, $this->_getServerMock());
    }


    /**
     * @expectedException \Beanie\Exception\InternalErrorException
     * @expectedExceptionCode 500
     */
    public function testParseLine_internalError_throwsInternalErrorException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this->getMockForAbstractClass('\Beanie\Command\AbstractCommand');


        $commandMock->parseResponse(Response::ERROR_INTERNAL_ERROR, $this->_getServerMock());
    }


    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testParseLine_notFound_throwsNotFoundException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this->getMockForAbstractClass('\Beanie\Command\AbstractCommand');


        $commandMock->parseResponse(Response::ERROR_NOT_FOUND, $this->_getServerMock());
    }


    /**
     * @expectedException \Beanie\Exception\OutOfMemoryException
     * @expectedExceptionCode 503
     */
    public function testParseLine_outOfMemory_throwsOutOfMemoryException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCommand $commandMock */
        $commandMock = $this->getMockForAbstractClass('\Beanie\Command\AbstractCommand');


        $commandMock->parseResponse(Response::ERROR_OUT_OF_MEMORY, $this->_getServerMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server $serverMock
     */
    protected function _getServerMock()
    {
        return $this
            ->getMockBuilder('\Beanie\Server\Server')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
