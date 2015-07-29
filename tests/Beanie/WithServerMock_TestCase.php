<?php


namespace Beanie;


use Beanie\Command\Response;
use Beanie\Server\ResponseOath;
use Beanie\Server\Server;

class WithServerMock_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $extraMethods
     * @return Server|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getServerMock($extraMethods = [])
    {
        $methods = array_merge(['__toString', 'readData'], $extraMethods);

        return $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    /**
     * @param string $data YAML data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server
     */
    protected function _getServerReturningYAMLData($data)
    {
        $serverMock = $this->getServerMock();
        $serverMock->expects($this->once())
            ->method('readData')
            ->with(strlen($data))
            ->willReturn($data);

        return $serverMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResponseOath
     */
    protected function _getResponseOathMock()
    {
        return $this
            ->getMockBuilder(ResponseOath::class)
            ->disableOriginalConstructor()
            ->setMethods(['invoke', 'getSocket'])
            ->getMock();
    }

    /**
     * @param Response $response
     * @return ResponseOath|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function oath(Response $response)
    {
        $oathMock = $this->_getResponseOathMock();
        $oathMock
            ->expects($this->once())
            ->method('invoke')
            ->willReturn($response);

        return $oathMock;
    }
}
