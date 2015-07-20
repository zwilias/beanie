<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Response;
use Beanie\Server\Server;

class StatsJobCommand extends AbstractWithYAMLResponseCommand
{
    /** @var int */
    protected $_jobId;

    /**
     * @param int $jobId
     */
    public function __construct($jobId)
    {
        $this->_jobId = (int) $jobId;
    }

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        if ($responseLine == Response::FAILURE_NOT_FOUND) {
            throw new NotFoundException($this, $server);
        }

        return parent::_parseResponse($responseLine, $server);
    }

    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_STATS_JOB, $this->_jobId);
    }
}
