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
}
