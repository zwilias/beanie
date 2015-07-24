<?php


namespace Beanie\Command\CommandLineCreator;


class PutCommandLineCreator extends GenericCommandLineCreator
{
    /**
     * @inheritDoc
     */
    public function __construct($commandName, array $arguments, array $argumentDefaults = [])
    {
        $this->setData(array_shift($arguments));

        parent::__construct($commandName, $arguments, $argumentDefaults);

        array_push($this->arguments, strlen($this->getData()));
    }
}
