<?php


namespace Beanie\Command;


use Beanie\Server\Server;

class WithServerMock_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server
     */
    protected function _getServerMock()
    {
        return $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'readData'])
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
        $serverMock = $this->_getServerMock();
        $serverMock->expects($this->once())
            ->method('readData')
            ->with(strlen($data))
            ->willReturn($data);

        return $serverMock;
    }
}
