<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class ReleaseCommand extends AbstractCommand
{
    /** @var int */
    protected $_jobId;

    /** @var int */
    protected $_priority;

    /** @var int */
    protected $_delay;

    public function __construct($jobId, $priority = Beanie::DEFAULT_PRIORITY, $delay = Beanie::DEFAULT_DELAY)
    {
        $this->_jobId = (int) $jobId;
        $this->_priority = (int) $priority;
        $this->_delay = (int) $delay;
    }

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
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
            $this->_jobId,
            $this->_priority,
            $this->_delay
        ]);
    }
}
