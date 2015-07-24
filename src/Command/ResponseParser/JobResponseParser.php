<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Server\Server;

class JobResponseParser extends AbstractDataResponseParser
{
    /**
     * @inheritDoc
     */
    protected function extractData($responseLine, Server $server)
    {
        list(, $id, $dataLength) = explode(' ', $responseLine);

        return [
            'id' => $id,
            'data' => $server->readData($dataLength)
        ];
    }
}
