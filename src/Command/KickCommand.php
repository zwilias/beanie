<?php


namespace Beanie\Command;


use Beanie\Beanie;


use Beanie\Server\Server;

class KickCommand extends AbstractCommand
{
    /** @var int */
    protected $maxToKick;

    /**
     * @param int $maxToKick
     */
    public function __construct($maxToKick = Beanie::DEFAULT_MAX_TO_KICK)
    {
        $this->maxToKick = (int) $maxToKick;
    }

    /**
     * @inheritDoc
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        list(, $kicked) = explode(' ', $responseLine);

        return new Response(Response::RESPONSE_KICKED, $kicked, $server);
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_KICK, $this->maxToKick);
    }
}
