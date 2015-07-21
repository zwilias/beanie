<?php


namespace Beanie\Command;


class WithServerMock_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server
     */
    protected function _getServerMock()
    {
        return $this
            ->getMockBuilder('\Beanie\Server\Server')
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
