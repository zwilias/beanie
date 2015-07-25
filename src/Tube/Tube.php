<?php


namespace Beanie\Tube;


use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Command\Response;
use Beanie\Exception;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Exception\NotFoundException;
use Beanie\Job\Job;
use Beanie\Job\JobFactory;
use Beanie\Server\Server;


class Tube implements TubeAware
{
    /** @var TubeStatus */
    protected $tubeStatus;

    /** @var Server */
    protected $server;

    /** @var JobFactory */
    protected $jobFactory;

    /** @var CommandFactory */
    protected $commandFactory;

    /**
     * @param string $tubeName
     * @param Server $server
     * @param JobFactory|null $jobFactory
     */
    public function __construct($tubeName, Server $server, JobFactory $jobFactory = null)
    {
        $this->tubeStatus = new TubeStatus();
        $this->tubeStatus->setCurrentTube($tubeName);
        $this->server = $server;

        $this->jobFactory = $jobFactory ?: JobFactory::instance();
        $this->commandFactory = CommandFactory::instance();
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
        $this->getTubeStatus()->transformTo($tubeStatus, $mode);
    }

    /**
     * @return Job|null If no job in the ready state was found, null is returned
     */
    public function peekReady()
    {
        return $this->peek($this->commandFactory->create(Command::COMMAND_PEEK_READY));
    }

    /**
     * @return Job|null If no job in the delayed state was found, null is returned
     */
    public function peekDelayed()
    {
        return $this->peek($this->commandFactory->create(Command::COMMAND_PEEK_DELAYED));
    }

    /**
     * @return Job|null If no job in the buried state was found, null is returned
     */
    public function peekBuried()
    {
        return $this->peek($this->commandFactory->create(Command::COMMAND_PEEK_BURIED));
    }

    /**
     * @param Command $command
     * @return Job|null
     * @throws Exception\InvalidArgumentException
     */
    protected function peek(Command $command)
    {
        $this->sync();

        return $this->jobFactory->createFromCommand(
            $command,
            $this->server
        );
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

        return (int) $this->server->dispatchCommand(
            $this->commandFactory->create(Command::COMMAND_KICK, [$howMany])
        )->getData();
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
                $this->commandFactory->create(Command::COMMAND_STATS_TUBE, [
                    $this->getTubeStatus()->getCurrentTube()
                ])
            )
            ->getData();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->tubeStatus->getCurrentTube();
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
            ->dispatchCommand(
                $this->commandFactory->create(Command::COMMAND_PAUSE_TUBE, [
                    $this->getTubeStatus()->getCurrentTube()
                ])
            )
            ->getName() == Response::RESPONSE_PAUSED;
    }
}
