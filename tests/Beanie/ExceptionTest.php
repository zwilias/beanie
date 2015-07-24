<?php


namespace Beanie;


use Beanie\Exception\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testWrap_wrapsBeanieExceptionWithOriginalMessageAndCode()
    {
        $originalMessage = 'test';
        $originalCode = 123;
        $originalException = new Exception($originalMessage, $originalCode);


        $wrappedException = Exception::wrap($originalException);


        $this->assertEquals($originalMessage, $wrappedException->getMessage());
        $this->assertEquals($originalCode, $wrappedException->getCode());
        $this->assertEquals($originalException, $wrappedException->getPrevious());
    }

    public function testWrap_withMessage_wrapsBeanieExceptionWithOriginalCode()
    {
        $originalMessage = 'test';
        $originalCode = 123;
        $originalException = new Exception($originalMessage, $originalCode);

        $newMessage = 'new';


        $wrappedException = Exception::wrap($originalException, $newMessage);


        $this->assertEquals($newMessage, $wrappedException->getMessage());
        $this->assertEquals($originalCode, $wrappedException->getCode());
        $this->assertEquals($originalException, $wrappedException->getPrevious());
    }

    public function testWrap_withMessageAndCode_wrapsBeanieException()
    {
        $originalMessage = 'test';
        $originalCode = 123;
        $originalException = new Exception($originalMessage, $originalCode);

        $newMessage = 'new';
        $newCode = 456;


        $wrappedException = Exception::wrap($originalException, $newMessage, $newCode);


        $this->assertEquals($newMessage, $wrappedException->getMessage());
        $this->assertEquals($newCode, $wrappedException->getCode());
        $this->assertEquals($originalException, $wrappedException->getPrevious());
    }
}
