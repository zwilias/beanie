<?php

namespace {
    $nativeFunctionMock = null;


    class MockNativeTestCase extends \PHPUnit_Framework_TestCase
    {
        /**
         * @return \PHPUnit_Framework_MockObject_MockObject
         */
        protected function _getNativeFunctionMock()
        {
            global $nativeFunctionMock;

            if (! isset($nativeFunctionMock)) {
                $nativeFunctionMock = $this
                    ->getMockBuilder('stdClass')
                    ->setMethods(['socket_create', 'socket_last_error', 'socket_strerror'])
                    ->getMock()
                ;
            }

            return $nativeFunctionMock;
        }

        protected function _socketCreateSuccess()
        {
            $this->_getNativeFunctionMock()
                ->expects($this->once())
                ->method('socket_create')
                ->with(AF_INET, SOCK_STREAM, SOL_TCP)
                ->willReturn(true)
            ;
        }

        protected function _socketCreateFail($errorCode, $errorMessage)
        {
            $this->_getNativeFunctionMock()
                ->expects($this->once())
                ->method('socket_create')
                ->with(AF_INET, SOCK_STREAM, SOL_TCP)
                ->willReturn(false)
            ;

            $this->_getNativeFunctionMock()
                ->expects($this->once())
                ->method('socket_last_error')
                ->willReturn($errorCode)
            ;

            $this->_getNativeFunctionMock()
                ->expects($this->once())
                ->method('socket_strerror')
                ->with($errorCode)
                ->willReturn($errorMessage)
            ;
        }
    }
}

namespace Beanie\Server {
    function socket_create()
    {
        global $nativeFunctionMock;
        return call_user_func_array([$nativeFunctionMock, 'socket_create'], func_get_args());
    }

    function socket_last_error()
    {
        global $nativeFunctionMock;
        return call_user_func_array([$nativeFunctionMock, 'socket_last_error'], func_get_args());
    }

    function socket_strerror()
    {
        global $nativeFunctionMock;
        return call_user_func_array([$nativeFunctionMock, 'socket_strerror'], func_get_args());
    }
}
