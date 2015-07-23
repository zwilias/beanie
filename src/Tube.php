<?php


namespace Beanie;


use Beanie\Command\AbstractCommand;
use Beanie\Command\KickCommand;
use Beanie\Command\PauseTubeCommand;
use Beanie\Command\PeekBuriedCommand;
use Beanie\Command\PeekDelayedCommand;
use Beanie\Command\PeekReadyCommand;
use Beanie\Command\StatsTubeCommand;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Exception\NotFoundException;
use Beanie\Job\Factory;
use Beanie\Server\Server;
use Beanie\Server\TubeStatus;

class Tube implements TubeAware
{
    /** @var TubeStatus */
    protected $tubeStatus;

    /** @var Server */
    protected $server;

    /** @var Factory */
    protected $jobFactory;

    /**
     * @param string $tubeName
     * @param Server $server
     * @param Factory|null $jobFactory
     */
    public function __construct($tubeName, Server $server, Factory $jobFactory = null)
    {
        $this->tubeStatus = new TubeStatus();
        $this->tubeStatus->setCurrentTube($tubeName);
        $this->server = $server;

        $this->jobFactory = $jobFactory ?: new Factory();
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
    public function transformTubeStatusTo(TubeStatus $tubeStatus, $mode = TubeStatus::TRANSFORM_USE)
    {
        if ($mode & TubeStatus::TRANSFORM_USE) {
            $this->getTubeStatus()->setCurrentTube(
                $tubeStatus->getCurrentTube()
            );
        }
    }

    /**
     * @return Job|null If no job in the ready state was found, null is returned
     */
    public function peekReady()
    {
        return $this->peek(new PeekReadyCommand());
    }

    /**
     * @return Job|null If no job in the delayed state was found, null is returned
     */
    public function peekDelayed()
    {
        return $this->peek(new PeekDelayedCommand());
    }

    /**
     * @return Job|null If no job in the buried state was found, null is returned
     */
    public function peekBuried()
    {
        return $this->peek(new PeekBuriedCommand());
    }

    /**
     * @param AbstractCommand $command
     * @return Job|null
     * @throws Exception\InvalidArgumentException
     */
    protected function peek(AbstractCommand $command)
    {
        $this->sync();

        try {
            $response = $this->server->dispatchCommand($command);
            return $this->jobFactory->createFrom($response);
        } catch (NotFoundException $notFound) {
            return null;
        }
    }

    protected function sync()
    {
        $this->server->transformTubeStatusTo($this->getTubeStatus(), TubeStatus::TRANSFORM_USE);
    }

    /**
     * @param int $howMany
     * @return int
     * @throws InvalidArgumentException
     */
    public function kick($howMany)
    {
        if (!is_int($howMany) || $howMany < 1) {
            throw new InvalidArgumentException('Kick requires a strictly positive number of jobs to kick');
        }

        $this->sync();

        return (int) $this->server->dispatchCommand(new KickCommand($howMany))->getData();
    }

    /**
     * @return array
     * @throws NotFoundException If this tube has no data, i.e. it does not exist.
     */
    public function stats()
    {
        $this->sync();

        return (array) $this
            ->server
            ->dispatchCommand(
                new StatsTubeCommand(
                    $this->getTubeStatus()->getCurrentTube()
                )
            )
            ->getData();
    }

    /**
     * @param int $howLong
     * @return bool
     * @throws InvalidArgumentException
     * @throws NotFoundException If this tube does not exist, it cannot be paused.
     */
    public function pause($howLong)
    {
        if (!is_int($howLong) || $howLong < 0) {
            throw new InvalidArgumentException('Must pause for 0 or more seconds');
        }

        $this->sync();

        return $this->server
            ->dispatchCommand(new PauseTubeCommand($this->getTubeStatus()->getCurrentTube()))
            ->getName() == Response::RESPONSE_PAUSED;
    }
}
