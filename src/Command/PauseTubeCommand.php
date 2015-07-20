<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class PauseTubeCommand extends AbstractCommand
{
    /** @var string */
    protected $_tubeName;

    /** @var int */
    protected $_delay;

    /**
     * @param string $tubeName
     * @param int $delay
     */
    public function __construct($tubeName, $delay = Beanie::DEFAULT_DELAY)
    {
        $this->_ensureValidName($tubeName);

        $this->_tubeName = (string) $tubeName;
        $this->_delay = (int) $delay;
    }

    /**
     * {@inheritdoc}
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_NOT_FOUND:
                throw new NotFoundException($this, $server);
            case Response::RESPONSE_PAUSED:
                return new Response(Response::RESPONSE_PAUSED, null, $server);
            default:
                throw new UnexpectedResponseException($responseLine, $this, $server);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return join(' ', [
            Command::COMMAND_PAUSE_TUBE,
            $this->_tubeName,
            $this->_delay
        ]);
    }
}
