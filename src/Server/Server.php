<?php


namespace Beanie\Server;


use Beanie\Command\CommandInterface;
use Beanie\Exception\SocketException;
use Beanie\Manager;
use Beanie\Tube\TubeAwareInterface;
use Beanie\Tube\TubeStatus;

class Server implements TubeAwareInterface
{
    const DEFAULT_PORT = 11300;
    const DEFAULT_HOST = '127.0.0.1';

    const EOL = "\r\n";
    const EOL_LENGTH = 2;

    /** @var Socket */
    protected $socket;

    /** @var TubeStatus */
    protected $tubeStatus;

    /**
     * @param string $hostName
     * @param int $port
     * @throws SocketException
     */
    public function __construct($hostName = self::DEFAULT_HOST, $port = self::DEFAULT_PORT)
    {
        $this->socket = new Socket($hostName, $port);
        $this->tubeStatus = new TubeStatus();
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
     * @param \Beanie\Command\CommandInterface $command
     * @return \Beanie\Server\ResponseOath
     * @throws SocketException
     * @throws \Beanie\Exception\BadFormatException
     * @throws \Beanie\Exception\InternalErrorException
     * @throws \Beanie\Exception\OutOfMemoryException
     * @throws \Beanie\Exception\UnknownCommandException
     */
    public function dispatchCommand(CommandInterface $command)
    {
        $this->ensureConnected();
        $this->socket->write($command->getCommandLine() . self::EOL);

        if ($command->hasData()) {
            $this->socket->write($command->getData() . self::EOL);
        }

        return new ResponseOath($this->socket, $this, $command);
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return new Manager($this);
    }

    /**
     * @return string hostname:port
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->socket->getHostname(), $this->socket->getPort());
    }

    /**
     * @throws SocketException
     */
    public function connect()
    {
        $this->socket->connect();
    }

    /**
     * @throws SocketException
     */
    public function disconnect()
    {
        $this->socket->disconnect();
    }

    protected function ensureConnected()
    {
        if (!$this->socket->isConnected()) {
            $this->connect();
        }
    }

    /**
     * @param TubeStatus $goal
     * @param int $mode
     * @return $this
     */
    public function transformTubeStatusTo(TubeStatus $goal, $mode = TubeStatus::TRANSFORM_BOTH)
    {
        foreach ($this->tubeStatus->transformTo($goal, $mode) as $transformCommand) {
            $this->dispatchCommand($transformCommand)->invoke();
        }

        return $this;
    }

    /**
     * @return TubeStatus
     */
    public function getTubeStatus()
    {
        return $this->tubeStatus;
    }
}
