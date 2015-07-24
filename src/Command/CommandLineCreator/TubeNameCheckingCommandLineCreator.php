<?php


namespace Beanie\Command\CommandLineCreator;


use Beanie\Exception\InvalidNameException;
use Beanie\ValidNameChecker;

class TubeNameCheckingCommandLineCreator extends GenericCommandLineCreator
{
    /**
     * @var ValidNameChecker
     */
    private $validNameChecker;

    /**
     * @inheritdoc
     */
    public function __construct($commandName, array $arguments, array $argumentDefaults = [])
    {
        $this->validNameChecker = new ValidNameChecker();
        parent::__construct($commandName, $arguments, $argumentDefaults);
    }

    /**
     * @param array $arguments
     * @throws InvalidNameException
     */
    protected function setArguments(array $arguments)
    {
        $tubeName = current($arguments);

        $this->validNameChecker->ensureValidName($tubeName);

        parent::setArguments($arguments);
    }
}
