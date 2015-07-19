<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Response;
use Beanie\Server\Server;

class KickCommand extends AbstractCommand
{
    /** @var int */
    protected $_maxToKick;

    /**
     * @param int $maxToKick
     */
    public function __construct($maxToKick = Beanie::DEFAULT_MAX_TO_KICK)
    {
        $this->_maxToKick = (int)$maxToKick;
    }

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        list(, $kicked) = explode(' ', $responseLine);

        return new Response(Response::RESPONSE_KICKED, $kicked, $server);
    }

    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_KICK, $this->_maxToKick);
    }
}
