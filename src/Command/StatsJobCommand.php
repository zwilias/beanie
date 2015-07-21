<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Response;
use Beanie\Server\Server;

class StatsJobCommand extends AbstractWithYAMLResponseCommand
{
    /** @var int */
    protected $jobId;

    /**
     * @param int $jobId
     */
    public function __construct($jobId)
    {
        $this->jobId = (int) $jobId;
    }

    /**
     * @inheritDoc
     */
    protected function parseResponseLine($responseLine, Server $server)
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
        return sprintf('%s %s', Command::COMMAND_STATS_JOB, $this->jobId);
    }
}
