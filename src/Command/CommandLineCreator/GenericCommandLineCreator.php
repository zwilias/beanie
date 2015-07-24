<?php


namespace Beanie\Command\CommandLineCreator;


use Beanie\Exception\InvalidArgumentException;

class GenericCommandLineCreator implements CommandLineCreator
{
    /** @var string */
    private $commandName;

    /** @var array */
    protected $arguments;

    /** @var string */
    private $data = null;

    /**
     * @param string $commandName
     * @param array $arguments
     * @param array $argumentDefaults
     */
    public function __construct($commandName, array $arguments, array $argumentDefaults = [])
    {
        $this->commandName = $commandName;
        $this->initArguments(array_values($arguments), $argumentDefaults);
    }

    private function initArguments($arguments, $defaults)
    {
        foreach (array_keys($defaults) as $index => $key) {
            $arguments[$index] = $this->extractDefault($index, $key, $arguments, $defaults);
        }

        $this->setArguments($arguments);
    }

    /**
     * @param int $index
     * @param string $key
     * @param array $arguments
     * @param array $defaults
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function extractDefault($index, $key, $arguments, $defaults)
    {
        if (!(isset($arguments[$index]) || isset($defaults[$key]))) {
            throw new InvalidArgumentException(
                sprintf('Argument \'%s\' is required for \'%s\'', $key, $this->commandName)
            );
        }

        return isset($arguments[$index])
            ? $arguments[$index]
            : $defaults[$key];
    }

    /**
     * @inheritdoc
     */
    public function hasData()
    {
        return $this->data !== null;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getCommandLine()
    {
        return join(' ', array_merge([$this->commandName], $this->arguments));
    }

    /**
     * @param array $arguments
     */
    protected function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $data
     */
    protected function setData($data)
    {
        $this->data = $data;
    }
}
