<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Server\Server;

class SimpleValueResponseParser extends AbstractDataResponseParser
{
    /**
     * @inheritdoc
     */
    protected function extractData($responseLine, Server $server)
    {
        list(, $data) = explode(' ', $responseLine, 2);
        return $data;
    }
}
