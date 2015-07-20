<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Response;
use Beanie\Server\Server;

class StatsTubeCommand extends AbstractWithYAMLResponseCommand
{
    /** @var string */
    protected $_tubeName;

    /**
     * @param string $tubeName
     * @throws \Beanie\Exception\InvalidNameException
     */
    public function __construct($tubeName)
    {
        $this->_ensureValidName($tubeName);
        $this->_tubeName = (string) $tubeName;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, Server $server)
    {
        if ($responseLine == Response::FAILURE_NOT_FOUND) {
            throw new NotFoundException($this, $server);
        }

        return parent::parseResponse($responseLine, $server);
    }


    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_STATS_TUBE, $this->_tubeName);
    }
}
