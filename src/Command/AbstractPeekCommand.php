<?php


namespace Beanie\Command;


use Beanie\Exception\NotFoundException;
use Beanie\Response;
use Beanie\Server\Server;

abstract class AbstractPeekCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     * @throws \Beanie\Exception\NotFoundException
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        if ($responseLine == Response::FAILURE_NOT_FOUND) {
            throw new NotFoundException($this, $server);
        }

        list(, $jobId, $dataLength) = explode(' ', $responseLine, 3);

        return new Response(Response::RESPONSE_FOUND, [
            'id' => $jobId,
            'data' => $server->readData($dataLength)
        ], $server);
    }
}
