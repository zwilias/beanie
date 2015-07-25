<?php


namespace Beanie\Command\CommandLineCreator;


class GenericCommandLineCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Beanie\Exception\InvalidArgumentException
     */
    public function testCreate_missingRequiredArgument_throwsException()
    {
        $defaults = [
            'arg' => null
        ];

        new GenericCommandLineCreator('test', [], $defaults);
    }

    public function testCreate_hasNoData()
    {
        $commandLineCreator = new GenericCommandLineCreator('test', []);


        $this->assertFalse($commandLineCreator->hasData());
        $this->assertNull($commandLineCreator->getData());
        $this->assertEquals('test', $commandLineCreator->getCommandLine());
    }
}
