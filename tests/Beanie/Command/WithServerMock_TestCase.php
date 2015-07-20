<?php


namespace Beanie\Command;


class WithServerMock_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Beanie\Server\Server $serverMock
     */
    protected function _getServerMock()
    {
        return $this
            ->getMockBuilder('\Beanie\Server\Server')
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'getData'])
            ->getMock()
        ;
    }
}
