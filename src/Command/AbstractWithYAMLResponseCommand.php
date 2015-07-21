<?php


namespace Beanie\Command;


use Beanie\Exception;
use Beanie\Response;
use Beanie\Server\Server;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractWithYAMLResponseCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        list(, $dataLength) = explode(' ', $responseLine, 2);

        $data = $server->getData($dataLength);

        try {
            $parsedData = Yaml::parse($data);
        } catch (ParseException $parseException) {
            throw Exception::wrap($parseException,
                sprintf('Failed to parse response for \'%s\' on \'s\'', get_class($this), (string) $server)
            );
        }

        return new Response(Response::RESPONSE_OK, $parsedData, $server);
    }
}
