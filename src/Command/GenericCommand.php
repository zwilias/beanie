<?php


namespace Beanie\Command;


use Beanie\Command\CommandLineCreator\CommandLineCreatorInterface;
use Beanie\Command\ResponseParser\ResponseParserInterface;
use Beanie\Server\Server;

class GenericCommand implements CommandInterface
{
    /** @var CommandLineCreatorInterface */
    protected $commandLineCreator;

    /** @var ResponseParserInterface */
    protected $responseParser;

    /**
     * @param CommandLineCreatorInterface $commandLineCreator
     * @param ResponseParserInterface $responseParser
     */
    public function __construct(CommandLineCreatorInterface $commandLineCreator, ResponseParserInterface $responseParser)
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
