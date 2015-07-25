<?php


namespace Beanie\Command;

require_once 'WithServerMock_TestCase.php';

class CommandFactoryTest extends WithServerMock_TestCase
{
    const TEST_ID = 999;

    /**
     * @var CommandFactory
     */
    private $commandFactory;

    public function setUp()
    {
        $this->commandFactory = CommandFactory::instance();
    }

    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testcreate_UnknownCommand_ThrowsException()
    {
        $this->commandFactory->create('UNKNOWN');
    }
}
