<?php


namespace Beanie\Job;


use Beanie\Server\Server;

class Job
{
    const STATE_UNKNOWN = null;
    const STATE_RELEASED = 'RELEASED';
    const STATE_RESERVED = 'RESERVED';
    const STATE_BURIED = 'BURIED';
    const STATE_DELETED = 'DELETED';

    /** @var int */
    protected $id;

    /** @var mixed */
    protected $data;

    /** @var Server */
    protected $server;

    /** @var null|string */
    protected $state;

    /**
     * @param int $id
     * @param mixed $data
     * @param Server $server
     * @param null|string $state
     */
    public function __construct($id, $data, Server $server, $state = self::STATE_UNKNOWN)
    {
        $this->id = (int) $id;
        $this->data = $data;
        $this->server = $server;
        $this->state = $state;
    }

    /**
     * @return null|string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
