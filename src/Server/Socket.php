<?php


namespace Beanie\Server;


use Beanie\Exception\SocketException;

class Socket
{
    /**
     * Longest possible response preface:
     * <code>USING <tubeName=max 200 byte>\r\n</code>
     */
    const MAX_SINGLE_RESPONSE_LENGTH = 208;

    /** @var bool */
    protected $connected = false;

    /** @var string */
    protected $hostname;

    /** @var int */
    protected $port;

    /** @var resource */
    protected $socket;

    /** @var string */
    private $readBuffer = '';

    /**
     * @param string $hostname
     * @param int $port
     * @throws SocketException When socket creation fails. Will have the underlying code and message.
     */
    public function __construct($hostname = Server::DEFAULT_HOST, $port = Server::DEFAULT_PORT)
    {
        $this->hostname = (string) $hostname;
        $this->port = (int) $port;

        if (($this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
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
        $this->ensureConnected();
        $dataLength = $leftToWrite = strlen($data);

        do {
            if (
                ($written = socket_write(
                    $this->socket,
                    substr($data, -$leftToWrite),
                    $leftToWrite
                )) === false
            ) {
                throw $this->createSocketException();
            }

            $leftToWrite -= $written;
        } while ($leftToWrite > 0);

        return $dataLength;
    }

    /**
     * Reads data from the connected socket in chunks of MAX_SINGLE_RESPONSE_LENGTH
     *
     * The MAX_SINGLE_RESPONSE_LENGTH should always capture at least the first line of a response, assuming the longest
     * possible response is the USING <tubename>\r\n response, which could be up to 208 bytes in length, for any valid
     * <tubename>. As a result, this function will, in reality, read some overflow of data for any response containing
     * data. This data is saved in a read-buffer, which `readData()` will read from first.
     *
     * @see readData()
     *
     * @param string $endOfLine
     * @return string
     * @throws SocketException
     */
    public function readLine($endOfLine = Server::EOL)
    {
        $this->ensureConnected();
        $buffer = '';

        do {
            $buffer .= $this->read(self::MAX_SINGLE_RESPONSE_LENGTH);

            $eolPosition = strpos($buffer, $endOfLine);
        } while ($eolPosition === false);

        $this->readBuffer = substr($buffer, $eolPosition + strlen($endOfLine));
        return substr($buffer, 0, $eolPosition + strlen($endOfLine));
    }

    /**
     * Read exactly $bytes of data from the connected sockets
     *
     * If there is not enough data to read $bytes from the read-buffer created by a previous `readLine` call combined
     * with the actual socket, this will block until data becomes available. Use with care.
     *
     * @param int $bytes
     * @return string
     * @throws SocketException When the connection drops during reading
     */
    public function readData($bytes)
    {
        $this->ensureConnected();

        $read = strlen($this->readBuffer);
        $buffer = $this->readBuffer;
        $this->readBuffer = '';

        while ($read < $bytes) {
            $incoming = $this->read($bytes - $read);
            $buffer .= $incoming;
            $read += strlen($incoming);
        }

        return substr($buffer, 0, $bytes);
    }

    /**
     * @param int $bytes
     * @return bool
     * @throws SocketException
     */
    protected function read($bytes)
    {
        if (($incoming = socket_read($this->socket, $bytes)) === false) {
            throw $this->createSocketException();
        }

        return $incoming;
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return resource
     */
    public function getRaw()
    {
        return $this->socket;
    }

    /**
     * @throws SocketException When connecting fails
     */
    public function connect()
    {
        if (($this->connected = socket_connect($this->socket, $this->hostname, $this->port)) === false) {
            throw $this->createSocketException();
        }
    }

    /**
     * @throws SocketException Ensure the Socket is connected. If it is not, throws and exception
     */
    protected function ensureConnected()
    {
        if ($this->connected !== true) {
            throw new SocketException('Socket is not connected.');
        }
    }

    /**
     * @return SocketException
     */
    protected function createSocketException()
    {
        $errorCode = socket_last_error();
        $errorMessage = socket_strerror($errorCode);
        $exception = new SocketException($errorMessage, $errorCode);
        return $exception;
    }
}
