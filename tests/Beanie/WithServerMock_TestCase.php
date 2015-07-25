<?php


namespace Beanie;


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
}
