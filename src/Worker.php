<?php


namespace Beanie;


use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Exception\TimedOutException;
use Beanie\Job\Job;
use Beanie\Job\JobFactory;
use Beanie\Server\Server;
use Beanie\Tube\TubeAware;
use Beanie\Tube\TubeStatus;

class Worker implements TubeAware
{
    /** @var Server */
    protected $server;

    /** @var TubeStatus */
    protected $tubeStatus;

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
     * @inheritDoc
     */
    public function getTubeStatus()
    {
        return $this->tubeStatus;
    }

    /**
     * @inheritDoc
     */
    public function transformTubeStatusTo(TubeStatus $tubeStatus, $mode = TubeStatus::TRANSFORM_WATCHED)
    {
        $this->getTubeStatus()->transformTo($tubeStatus, $mode);
        return $this;
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
     * @param null|int $timeout
     * @return Job|null
     */
    public function reserve($timeout = null)
    {
        $command = Command::COMMAND_RESERVE;
        $arguments = [];

        if (! is_null($timeout)) {
            $command = Command::COMMAND_RESERVE_WITH_TIMEOUT;
            $arguments[] = $timeout;
        }

        try {
            return $this->jobFactory->createFromCommand(
                $this->commandFactory->create($command, $arguments),
                $this->server->transformTubeStatusTo($this->getTubeStatus())
            );
        } catch (TimedOutException $timeOut) {
            return null;
        }
    }
}
