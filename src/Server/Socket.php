<?php


namespace Beanie\Server;


use Beanie\Exception\SocketException;

class Socket
{
    /**
     * Longest possible response preface:
     * USING <tubeName=max 200 byte>\r\n
     */
    const MAX_SINGLE_RESPONSE_LENGTH = 208;

    /** @var bool */
    protected $_connected = false;

    /** @var string */
    protected $_hostname;

    /** @var int */
    protected $_port;

    /** @var resource */
    protected $_socket;

    /** @var string */
    private $_readBuffer = '';

    /**
     * @param string $hostname
     * @param int $port
     * @throws SocketException When socket creation fails. Will have the underlying code and message.
     */
    public function __construct($hostname = Server::DEFAULT_HOST, $port = Server::DEFAULT_PORT)
    {
        $this->_hostname = (string)$hostname;
        $this->_port = (int)$port;

        if (($this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new SocketException($errorMessage, $errorCode);
        }
    }

    /**
     * @param string $data
     * @return int bytes written
     * @throws SocketException When the connection is dropped in the middle of writing.
     */
    public function write($data)
    {
        $this->_ensureConnected();
        $dataLength = $leftToWrite = strlen($data);

        do {
            if (
                ($written = socket_write(
                    $this->_socket,
                    substr($data, -$leftToWrite),
                    $leftToWrite
                )) === false
            ) {
                $errorCode = socket_last_error();
                $errorMessage = socket_strerror($errorCode);
                throw new SocketException($errorMessage, $errorCode);
            }

            $leftToWrite -= $written;
        } while ($leftToWrite > 0);

        return $dataLength;
    }

    /**
     * @return string
     * @throws SocketException When the connection drops during reading
     */
    public function readLine()
    {
        $this->_ensureConnected();
        $buffer = '';

        do {
            if (($incoming = socket_read($this->_socket, self::MAX_SINGLE_RESPONSE_LENGTH)) === false) {
                $errorCode = socket_last_error();
                $errorMessage = socket_strerror($errorCode);
                throw new SocketException($errorMessage, $errorCode);
            }

            $buffer .= $incoming;

            $eolPosition = strpos($buffer, Server::EOL);
        } while ($eolPosition === false);

        $this->_readBuffer = substr($buffer, $eolPosition + Server::EOL_LENGTH);
        return substr($buffer, 0, $eolPosition + Server::EOL_LENGTH);
    }

    /**
     * @param int $bytes
     * @return string
     * @throws SocketException When the connection drops during reading
     */
    public function readData($bytes)
    {
        $this->_ensureConnected();

        $read = strlen($this->_readBuffer);
        $buffer = $this->_readBuffer;
        $this->_readBuffer = '';

        while ($read < $bytes) {
            if (($incoming = socket_read($this->_socket, ($bytes - $read))) === false) {
                $errorCode = socket_last_error();
                $errorMessage = socket_strerror($errorCode);
                throw new SocketException($errorMessage, $errorCode);
            }

            $buffer .= $incoming;
            $read += strlen($incoming);
        }

        return substr($buffer, 0, $bytes);
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->_connected;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->_hostname;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * @return resource
     */
    public function getRaw()
    {
        return $this->_socket;
    }

    /**
     * @throws SocketException When connecting fails
     */
    public function connect()
    {
        if (($this->_connected = socket_connect($this->_socket, $this->_hostname, $this->_port)) === false) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new SocketException($errorMessage, $errorCode);
        }
    }

    /**
     * @throws SocketException Ensure the Socket is connected. If it is not, throws and exception
     */
    protected function _ensureConnected()
    {
        if ($this->_connected !== true) {
            throw new SocketException('Socket is not connected.');
        }
    }
}
