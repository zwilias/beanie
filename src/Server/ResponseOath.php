<?php


namespace Beanie\Server;


use Beanie\Command\ResponseParser\ResponseParser;
use Beanie\Oath;

class ResponseOath implements Oath
{
    /** @var Socket */
    protected $socket;

    /** @var Server */
    protected $server;

    /** @var ResponseParser */
    protected $responseParser;

    /**
     * @param Socket $socket
     * @param Server $server
     * @param ResponseParser $responseParser
     */
    public function __construct(Socket $socket, Server $server, ResponseParser $responseParser)
    {
        $this->socket = $socket;
        $this->server = $server;
        $this->responseParser = $responseParser;
    }

    /**
     * @return resource
     */
    public function getSocket()
    {
        return $this->socket->getRaw();
    }

    /**
     * @return \Beanie\Command\Response
     */
    public function invoke()
    {
        $responseLine = $this->socket->readLine(Server::EOL);

        return $this->responseParser->parseResponse(
            substr($responseLine, 0, strlen($responseLine) - Server::EOL_LENGTH),
            $this->server
        );
    }
}
