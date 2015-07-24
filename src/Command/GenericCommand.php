<?php


namespace Beanie\Command;


use Beanie\Command\CommandLineCreator\CommandLineCreator;
use Beanie\Command\ResponseParser\ResponseParser;
use Beanie\Server\Server;

class GenericCommand implements Command
{
    /** @var CommandLineCreator */
    protected $commandLineCreator;

    /** @var ResponseParser */
    protected $responseParser;

    /**
     * @param CommandLineCreator $commandLineCreator
     * @param ResponseParser $responseParser
     */
    public function __construct(CommandLineCreator $commandLineCreator, ResponseParser $responseParser)
    {
        $this->commandLineCreator = $commandLineCreator;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return $this->commandLineCreator->getCommandLine();
    }

    /**
     * @inheritDoc
     */
    public function hasData()
    {
        return $this->commandLineCreator->hasData();
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->commandLineCreator->getData();
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, Server $server)
    {
        return $this->responseParser->parseResponse($responseLine, $server);
    }
}
