<?php


namespace Beanie\Command;



use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;

use Beanie\Server\Server;

class TouchCommand extends AbstractCommand
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
     * @throws NotFoundException
     * @throws UnexpectedResponseException
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_TOUCHED:
                return new Response(Response::RESPONSE_TOUCHED, null, $server);
            default:
                throw new UnexpectedResponseException($responseLine, $this, $server);
        }
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_TOUCH, $this->jobId);
    }
}
