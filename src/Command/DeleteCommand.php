<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class DeleteCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $_jobId;

    /**
     * @param int $jobId
     */
    public function __construct($jobId)
    {
        $this->_jobId = (int)$jobId;
    }

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_DELETED:
                return new Response($responseLine, null, $server);
        }

        throw new UnexpectedResponseException($responseLine, $this, $server);
    }

    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_DELETE, $this->_jobId);
    }
}