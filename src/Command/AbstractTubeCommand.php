<?php


namespace Beanie\Command;


use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

abstract class AbstractTubeCommand extends AbstractCommand
{
    protected $tubeName;

    public function __construct($tubeName)
    {
        $this->ensureValidName($tubeName);
        $this->tubeName = $tubeName;
    }

    /**
     * @return string
     */
    abstract protected function getExpectedResponseName();

    /**
     * @return string
     */
    abstract protected function getCommandName();

    /**
     * @inheritDoc
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        list($responseName, $responseValue) = explode(' ', $responseLine, 2);
        $expectedResponseName = $this->getExpectedResponseName();

        if ($responseName !== $expectedResponseName) {
            throw new UnexpectedResponseException($responseName, $this, $server);
        }

        return new Response($expectedResponseName, $responseValue, $server);
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', $this->getCommandName(), $this->tubeName);
    }

}
