<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class KickJobCommand extends AbstractCommand
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
     * {@inheritdoc}
     * @throws \Beanie\Exception\NotFoundException
     * @throws \Beanie\Exception\UnexpectedResponseException
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_KICKED:
                return new Response($responseLine, null, $server);
            default:
                throw new UnexpectedResponseException($responseLine, $this, $server);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_KICK_JOB, $this->jobId);
    }
}
