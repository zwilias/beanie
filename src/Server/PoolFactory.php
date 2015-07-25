<?php


namespace Beanie\Server;


use Beanie\Util\FactoryTrait;

class PoolFactory
{
    use FactoryTrait;

    /**
     * @param array $serverList
     * @return Pool
     */
    public function create(array $serverList)
    {
        $servers = [];

        foreach ($serverList as $server) {
            list($host, $port) = $this->extractHostAndPort($server);
            $servers[] = $this->createServer($host, $port);
        }

        return new Pool($servers);
    }

    /**
     * @param string $server
     * @return array
     */
    protected function extractHostAndPort($server)
    {
        return (strpos($server, ':') !== false)
            ? explode(':', $server, 2)
            : [$server, Server::DEFAULT_PORT]
        ;
    }

    /**
     * @param string $host
     * @param int $port
     * @return Server
     */
    public function createServer($host, $port)
    {
        return new Server($host, $port);
    }
}
