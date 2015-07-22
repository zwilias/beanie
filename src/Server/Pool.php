<?php


namespace Beanie\Server;


use Beanie\Command\AbstractCommand;
use Beanie\Exception\InvalidArgumentException;

class Pool
{
    /** @var Server[] */
    protected $servers = [];

    /** @var TubeStatus */
    protected $tubeStatus;

    /**
     * @param Server[] $servers
     * @throws InvalidArgumentException
     */
    public function __construct(array $servers)
    {
        if (!count($servers)) {
            throw new InvalidArgumentException('Pool needs servers');
        }

        foreach ($servers as $server) {
            $this->addServer($server);
        }

        $this->tubeStatus = new TubeStatus();
    }

    /**
     * @param Server $server
     * @return $this
     */
    protected function addServer(Server $server)
    {
        $this->servers[(string) $server] = $server;
        return $this;
    }

    /**
     * @return Server[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @param string $name
     * @return Server
     * @throws InvalidArgumentException
     */
    public function getServer($name)
    {
        if (!isset($this->servers[$name])) {
            throw new InvalidArgumentException('Server not found by name: ' . $name);
        }

        return $this->servers[$name];
    }

    /**
     * @param AbstractCommand $command
     * @return \Beanie\Response
     */
    public function dispatchCommand(AbstractCommand $command)
    {
        return $this->synchronizeTubes($this->getRandomServer())->dispatchCommand($command);
    }

    /**
     * @return TubeStatus
     */
    public function getTubeStatus()
    {
        return $this->tubeStatus;
    }

    /**
     * @return Server
     */
    protected function getRandomServer()
    {
        return $this->servers[array_rand($this->servers, 1)];
    }

    /**
     * @param Server $server
     * @return Server
     */
    protected function synchronizeTubes(Server $server)
    {
        foreach ($server->getTubeStatus()->getCommandsToTransformTo($this->getTubeStatus()) as $transformCommand) {
            $server->dispatchCommand($transformCommand);
        }

        $server->getTubeStatus()
            ->setCurrentTube($this->getTubeStatus()->getCurrentTube())
            ->setWatchedTubes($this->getTubeStatus()->getWatchedTubes());

        return $server;
    }
}
