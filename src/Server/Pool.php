<?php


namespace Beanie\Server;


use Beanie\Command\CommandInterface;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Tube\TubeAwareInterface;
use Beanie\Tube\TubeStatus;

class Pool implements TubeAwareInterface
{
    use TubeAwareTrait;

    /** @var Server[] */
    protected $servers = [];

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
     * @param CommandInterface $command
     * @return \Beanie\Server\ResponseOath
     */
    public function dispatchCommand(CommandInterface $command)
    {
        return $this
            ->getRandomServer()
            ->transformTubeStatusTo($this->tubeStatus, TubeStatus::TRANSFORM_USE)
            ->dispatchCommand($command);
    }

    /**
     * @return Server
     */
    public function getRandomServer()
    {
        return $this->servers[array_rand($this->servers, 1)];
    }
}
