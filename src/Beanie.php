<?php


namespace Beanie;


use Beanie\Server\Pool;
use Beanie\Server\PoolFactory;
use Beanie\Server\Server;

class Beanie
{
    const DEFAULT_TUBE = 'default';
    const DEFAULT_PRIORITY = 1024;
    const DEFAULT_DELAY = 0;
    const DEFAULT_TIME_TO_RUN = 60;
    const DEFAULT_MAX_TO_KICK = 50;

    /** @var Pool  */
    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param null|string $serverName
     * @return Worker
     * @throws Exception\InvalidArgumentException
     */
    public function worker($serverName = null)
    {
        $server = ($serverName === null)
            ? $this->pool->getRandomServer()
            : $this->pool->getServer($serverName);

        return new Worker($server);
    }

    /**
     * @return Worker[]
     */
    public function workers()
    {
        return array_map(function (Server $server) {
            return new Worker($server);
        }, $this->pool->getServers());
    }

    /**
     * @return Producer
     */
    public function producer()
    {
        return new Producer($this->pool);
    }

    /**
     * @param string $serverName
     * @return Manager
     * @throws Exception\InvalidArgumentException
     */
    public function manager($serverName)
    {
        return $this->pool->getServer($serverName)->getManager();
    }

    /**
     * @return Manager[]
     */
    public function managers()
    {
        return array_map(function (Server $server) {
            return $server->getManager();
        }, $this->pool->getServers());
    }

    /**
     * @param string[] $servers
     * @param PoolFactory|null $poolFactory
     * @return static
     */
    public static function pool($servers, PoolFactory $poolFactory = null)
    {
        $poolFactory = $poolFactory ?: PoolFactory::instance();
        return new static($poolFactory->create($servers));
    }
}
