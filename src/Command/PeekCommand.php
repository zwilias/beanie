<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Response;
use Beanie\Server\Server;

class PeekCommand extends AbstractCommand
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

        list(, $jobId, $dataLength) = explode(' ', $responseLine, 3);

        return new Response(Response::RESPONSE_FOUND, [
            'id' => $jobId,
            'data' => $server->getData($dataLength)
        ], $server);
    }

    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_PEEK, $this->_jobId);
    }
}
