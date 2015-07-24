<?php


namespace Beanie\Command;


use Beanie\Beanie;

use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;

use Beanie\Server\Server;

class ReleaseCommand extends AbstractCommand
{
    /** @var int */
    protected $jobId;

    /** @var int */
    protected $priority;

    /** @var int */
    protected $delay;

    public function __construct($jobId, $priority = Beanie::DEFAULT_PRIORITY, $delay = Beanie::DEFAULT_DELAY)
    {
        $this->jobId = (int) $jobId;
        $this->priority = (int) $priority;
        $this->delay = (int) $delay;
    }

    /**
     * @inheritDoc
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_BURIED:
            case Response::RESPONSE_RELEASED:
                return new Response($responseLine, null, $server);
            default:
                throw new UnexpectedResponseException($responseLine, $this, $server);
        }
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return join(' ', [
            Command::COMMAND_RELEASE,
            $this->jobId,
            $this->priority,
            $this->delay
        ]);
    }
}
