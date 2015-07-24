<?php


namespace Beanie\Command\ResponseParser;


use Beanie\Exception\Exception;
use Beanie\Server\Server;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YAMLResponseParser extends AbstractDataResponseParser
{
    /**
     * @inheritDoc
     */
    protected function extractData($responseLine, Server $server)
    {
        list(, $dataLength) = explode(' ', $responseLine);
        $rawData = $server->readData($dataLength);

        try {
            return Yaml::parse($rawData);
        } catch (ParseException $exception) {
            throw Exception::wrap($exception);
        }
    }
}
