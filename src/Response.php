<?php


namespace Beanie;


use Beanie\Server\Server;

class Response
{
    const ERROR_OUT_OF_MEMORY = 'OUT_OF_MEMORY';
    const ERROR_INTERNAL_ERROR = 'INTERNAL_ERROR';
    const ERROR_BAD_FORMAT = 'BAD_FORMAT';
    const ERROR_NOT_FOUND = 'NOT_FOUND';

    const RESPONSE_USING = 'USING';
    const RESPONSE_WATCHING = 'WATCHING';

    /** @var string */
    protected $_name;

    /** @var mixed */
    protected $_data;

    /** @var Server */
    protected $_server;

    /**
     * @param string $name
     * @param string $data
     * @param Server $server
     */
    public function __construct($name, $data, Server $server)
    {
        $this->_name = $name;
        $this->_data = $data;
        $this->_server = $server;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->_server;
    }
}
