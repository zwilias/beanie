<?php


namespace Beanie\Command;


use Beanie\Server\Server;

class Response
{
    // GLOBAL ERRORS
    const ERROR_BAD_FORMAT = 'BAD_FORMAT';
    const ERROR_INTERNAL_ERROR = 'INTERNAL_ERROR';
    const ERROR_OUT_OF_MEMORY = 'OUT_OF_MEMORY';
    const ERROR_UNKNOWN_COMMAND = 'UNKNOWN_COMMAND';

    // FAILURE RESPONSES
    const FAILURE_DEADLINE_SOON = 'DEADLINE_SOON';
    const FAILURE_DRAINING = 'DRAINING';
    const FAILURE_EXPECTED_CRLF = 'EXPECTED_CRLF';
    const FAILURE_JOB_TOO_BIG = 'JOB_TOO_BIG';
    const FAILURE_NOT_FOUND = 'NOT_FOUND';
    const FAILURE_NOT_IGNORED = 'NOT_IGNORED';
    const FAILURE_TIMED_OUT = 'TIMED_OUT';

    // SUCCESSFUL RESPONSES
    const RESPONSE_BURIED = 'BURIED';
    const RESPONSE_DELETED = 'DELETED';
    const RESPONSE_FOUND = 'FOUND';
    const RESPONSE_INSERTED = 'INSERTED';
    const RESPONSE_KICKED = 'KICKED';
    const RESPONSE_OK = 'OK';
    const RESPONSE_PAUSED = 'PAUSED';
    const RESPONSE_RELEASED = 'RELEASED';
    const RESPONSE_RESERVED = 'RESERVED';
    const RESPONSE_TOUCHED = 'TOUCHED';
    const RESPONSE_USING = 'USING';
    const RESPONSE_WATCHING = 'WATCHING';

    /** @var string */
    protected $name;

    /** @var mixed */
    protected $data;

    /** @var Server */
    protected $server;

    /**
     * @param string $name
     * @param mixed $data
     * @param Server $server
     */
    public function __construct($name, $data, Server $server)
    {
        $this->name = $name;
        $this->data = $data;
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}
