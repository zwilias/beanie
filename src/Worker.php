<?php


namespace Beanie;


use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Exception\TimedOutException;
use Beanie\Job\Job;
use Beanie\Job\JobFactory;
use Beanie\Job\JobOath;
use Beanie\Server\Server;
use Beanie\Server\TubeAwareTrait;
use Beanie\Tube\TubeAware;
use Beanie\Tube\TubeStatus;

class Worker implements TubeAware
{
    use TubeAwareTrait;

    /** @var Server */
    protected $server;

    /** @var CommandFactory */
    protected $commandFactory;

    /** @var JobFactory */
    protected $jobFactory;

    /**
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->tubeStatus = new TubeStatus();
        $this->commandFactory = CommandFactory::instance();
        $this->jobFactory = JobFactory::instance();
    }

    /**
     * @param string $tubeName
     * @param bool|false $onlyThisTube
     * @return $this
     */
    public function watch($tubeName, $onlyThisTube = false)
    {
        if ($onlyThisTube) {
            $this->tubeStatus->setWatchedTubes([$tubeName]);
        } else {
            $this->tubeStatus->addWatchedTube($tubeName);
        }

        return $this;
    }

    /**
     * @param string $tubeName
     * @return $this
     */
    public function ignore($tubeName)
    {
        $this->tubeStatus->removeWatchedTube($tubeName);
        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    public function quit()
    {
        $this->server->dispatchCommand($this->commandFactory->create(Command::COMMAND_QUIT));
    }

    /**
     * @param null|int $timeout
     * @return Job|null
     */
    public function reserve($timeout = null)
    {
        $command = Command::COMMAND_RESERVE;
        $arguments = [];

        if (!is_null($timeout)) {
            $command = Command::COMMAND_RESERVE_WITH_TIMEOUT;
            $arguments[] = $timeout;
        }

        try {
            return $this->jobFactory->createFromCommand(
                $this->commandFactory->create($command, $arguments),
                $this->server->transformTubeStatusTo($this->getTubeStatus())
            )->invoke();
        } catch (TimedOutException $timeOut) {
            return null;
        }
    }

    /**
     * @return JobOath
     * @throws Exception\InvalidArgumentException
     */
    public function reserveOath()
    {
        return $this->jobFactory->createFromCommand(
            $this->commandFactory->create(Command::COMMAND_RESERVE),
            $this->server->transformTubeStatusTo($this->getTubeStatus(), TubeStatus::TRANSFORM_WATCHED)
        );
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->server;
    }

    public function reconnect()
    {
        $this->getServer()->connect();
    }

    public function disconnect()
    {
        $this->getServer()->disconnect();
    }
}
