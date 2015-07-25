<?php


namespace Beanie\Command;

require_once 'WithServerMock_TestCase.php';

class CommandFactoryTest extends WithServerMock_TestCase
{
    const TEST_ID = 999;

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testCreate_UnknownCommand_ThrowsException()
    {
        CommandFactory::instance()->create('UNKNOWN');
    }

    public function testInstance_returnsSame()
    {
        CommandFactory::unsetInstance();
        $firstInstance = CommandFactory::instance();

        $this->assertSame($firstInstance, CommandFactory::instance());
    }
}
