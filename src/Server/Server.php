<?php


namespace Beanie\Server;


use Beanie\Exception\SocketException;

class Server
{
    const DEFAULT_PORT = 11300;
    const DEFAULT_HOST = '127.0.0.1';

    const EOL = "\r\n";
    const EOL_LENGTH = 2;

    /** @var Socket */
    protected $_socket;

    /**
     * @param string $hostName
     * @param int $port
     * @throws SocketException
     */
    public function __construct($hostName = self::DEFAULT_HOST, $port = self::DEFAULT_PORT)
    {
        $this->_socket = new Socket($hostName, $port);
    }

    /**
     * @param int $bytes
     * @param int $extra
     *
     * @return string
     */
    public function getData($bytes, $extra = self::EOL_LENGTH)
    {
        // TODO: implement
    }

    /**
     * @return string hostname:port
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->_socket->getHostname(), $this->_socket->getPort());
    }
}
