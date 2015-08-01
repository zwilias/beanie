<?php

namespace Beanie\Server;

$nativeFunctionMock = null;
$mockedNativeFunctions = [
    'socket_create',
    'socket_last_error',
    'socket_strerror',
    'socket_connect',
    'socket_write',
    'socket_read',
    'socket_close'
];

$namespace = __NAMESPACE__;

foreach ($mockedNativeFunctions as $mockedFunction) {
    eval(<<<EOD
namespace {$namespace};

function {$mockedFunction}()
{
    global \$nativeFunctionMock;
    return is_callable([\$nativeFunctionMock, '{$mockedFunction}'])
        ? call_user_func_array([\$nativeFunctionMock, '{$mockedFunction}'], func_get_args())
        : call_user_func_array('{$mockedFunction}', func_get_args());
}
EOD
    );
}

class MockNative_TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        global $nativeFunctionMock;
        $nativeFunctionMock = null;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getNativeFunctionMock()
    {
        global $nativeFunctionMock, $mockedNativeFunctions;

        if (! isset($nativeFunctionMock)) {
            $nativeFunctionMock = $this
                ->getMockBuilder('stdClass')
                ->setMethods($mockedNativeFunctions)
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

        $this->_setSocketError($errorCode, $errorMessage);
    }

    protected function _socketConnectSuccess()
    {
        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_connect')
            ->willReturn(true);
    }

    protected function _socketConnectFail($host, $port, $errorCode, $errorMessage)
    {
        $this->_getNativeFunctionMock()
            ->expects($this->once())
            ->method('socket_connect')
            ->with($this->anything(), $host, $port)
            ->willReturn(false)
        ;

        $this->_setSocketError($errorCode, $errorMessage);
    }

    protected function _setSocketError($errorCode, $errorMessage)
    {
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
