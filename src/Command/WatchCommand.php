<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class WatchCommand extends AbstractCommand
{
    protected $_tubeName;

    public function __construct($tubeName)
    {
        $this->_ensureValidName($tubeName);
        $this->_tubeName = $tubeName;
    }

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        list($responseName, $countTubes) = explode(' ', $responseLine, 2);

        if ($responseName !== Response::RESPONSE_WATCHING) {
            throw new UnexpectedResponseException($responseName, $this, $server);
        }

        return new Response(Response::RESPONSE_WATCHING, (int)$countTubes, $server);
    }

    /**
     * @inheritDoc
     */
    function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_WATCH, $this->_tubeName);
    }
}
