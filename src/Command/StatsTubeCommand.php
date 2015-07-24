<?php


namespace Beanie\Command;



use Beanie\Exception\NotFoundException;

use Beanie\Server\Server;

class StatsTubeCommand extends AbstractWithYAMLResponseCommand
{
    /** @var string */
    protected $tubeName;

    /**
     * @param string $tubeName
     * @throws \Beanie\Exception\InvalidNameException
     */
    public function __construct($tubeName)
    {
        $this->ensureValidName($tubeName);
        $this->tubeName = (string) $tubeName;
    }

    /**
     * @inheritDoc
     */
    public function parseResponseLine($responseLine, Server $server)
    {
        if ($responseLine == Response::FAILURE_NOT_FOUND) {
            throw new NotFoundException($this, $server);
        }

        return parent::parseResponseLine($responseLine, $server);
    }


    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_STATS_TUBE, $this->tubeName);
    }
}
