<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class BuryCommand extends AbstractCommand
{
    /** @var int */
    protected $_jobId;

    /** @var int */
    protected $_priority;

    /**
     * @param int $jobId
     * @param int $priority
     */
    public function __construct($jobId, $priority = Beanie::DEFAULT_PRIORITY)
    {
        $this->_jobId = (int)$jobId;
        $this->_priority = (int)$priority;
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     * @throws UnexpectedResponseException
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_BURIED:
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
            Command::COMMAND_BURY,
            $this->_jobId,
            $this->_priority
        ]);
    }
}
