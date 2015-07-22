<?php


namespace Beanie\Server;


use Beanie\Command;
use Beanie\Exception\SocketException;

class Server
{
    const DEFAULT_PORT = 11300;
    const DEFAULT_HOST = '127.0.0.1';

    const EOL = "\r\n";
    const EOL_LENGTH = 2;

    /** @var Socket */
    protected $socket;

    /**
     * @param string $hostName
     * @param int $port
     * @throws SocketException
     */
    public function __construct($hostName = self::DEFAULT_HOST, $port = self::DEFAULT_PORT)
    {
        $this->socket = new Socket($hostName, $port);
    }

    /**
     * @param Socket $socket
     *
     * @return $this
     */
    public function setSocket(Socket $socket)
    {
        $this->socket = $socket;
        return $this;
    }

    /**
     * @param int $bytes
     * @param int $extra
     *
     * @return string
     */
    public function readData($bytes, $extra = self::EOL_LENGTH)
    {
        $this->ensureConnected();

        $data = $this->socket->readData($bytes + $extra);

        return substr($data, 0, $bytes);
    }

    /**
     * @param Command\AbstractCommand $command
     * @return \Beanie\Response
     * @throws SocketException
     * @throws \Beanie\Exception\BadFormatException
     * @throws \Beanie\Exception\InternalErrorException
     * @throws \Beanie\Exception\OutOfMemoryException
     * @throws \Beanie\Exception\UnknownCommandException
     */
    public function dispatchCommand(Command\AbstractCommand $command)
    {
        $this->ensureConnected();
        $this->socket->write($command->getCommandLine() . self::EOL);

        if ($command->hasData()) {
            $this->socket->write($command->getData() . self::EOL);
        }

        $responseLine = $this->socket->readLine(self::EOL);

        return $command->parseResponse(
            substr($responseLine, 0, strlen($responseLine) - self::EOL_LENGTH),
            $this
        );
    }

    /**
     * @return string hostname:port
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->socket->getHostname(), $this->socket->getPort());
    }

    protected function ensureConnected()
    {
        if (!$this->socket->isConnected()) {
            $this->socket->connect();
        }
    }
}
