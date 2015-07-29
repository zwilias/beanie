<?php


namespace Beanie\Job;


use Beanie\Beanie;
use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Command\Response;
use Beanie\Server\Server;

class Job
{
    const STATE_UNKNOWN = null;
    const STATE_RELEASED = 'RELEASED';
    const STATE_RESERVED = 'RESERVED';
    const STATE_BURIED = 'BURIED';
    const STATE_DELETED = 'DELETED';

    /** @var int */
    protected $id;

    /** @var mixed */
    protected $data;

    /** @var Server */
    protected $server;

    /** @var null|string */
    protected $state;

    /** @var CommandFactory */
    protected $commandFactory;

    /**
     * @param int $id
     * @param mixed $data
     * @param Server $server
     * @param null|string $state
     */
    public function __construct($id, $data, Server $server, $state = self::STATE_UNKNOWN)
    {
        $this->id = (int) $id;
        $this->data = $data;
        $this->server = $server;
        $this->state = $state;
        $this->commandFactory = CommandFactory::instance();
    }

    /**
     * @return null|string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function kick()
    {
        $this->executeCommand(Command::COMMAND_KICK_JOB);
        return $this;
    }

    /**
     * @param int $priority
     * @param int $delay
     * @return $this
     */
    public function release($priority = Beanie::DEFAULT_PRIORITY, $delay = Beanie::DEFAULT_DELAY)
    {
        $response = $this->executeCommand(Command::COMMAND_RELEASE, [$priority, $delay]);
        $this->state = $response->getName() === Response::RESPONSE_RELEASED
            ? self::STATE_RELEASED
            : self::STATE_BURIED;

        return $this;
    }

    /**
     * @return $this
     */
    public function touch()
    {
        $this->executeCommand(Command::COMMAND_TOUCH);
        return $this;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function bury($priority = Beanie::DEFAULT_PRIORITY)
    {
        $this->executeCommand(Command::COMMAND_BURY, [$priority]);
        $this->state = self::STATE_BURIED;
        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $this->executeCommand(Command::COMMAND_DELETE);
        $this->state = self::STATE_DELETED;
        return $this;
    }

    /**
     * @return array
     */
    public function stats()
    {
        return $this->executeCommand(Command::COMMAND_STATS_JOB)->getData();
    }

    /**
     * @param string $command
     * @param array $arguments
     * @return Response
     */
    private function executeCommand($command, $arguments = [])
    {
        return $this->server
            ->dispatchCommand($this->commandFactory->create($command, array_merge([$this->id], $arguments)))
            ->invoke();
    }
}
