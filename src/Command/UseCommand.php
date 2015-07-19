<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

class UseCommand extends AbstractCommand
{
    /** @var string */
    protected $_tubeName;

    /**
     * @param $tubeName
     * @throws \Beanie\Exception\InvalidNameException
     */
    public function __construct($tubeName)
    {
        $this->_ensureValidName($tubeName);

        $this->_tubeName = $tubeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_USE, $this->_tubeName);
    }

    /**
     * {@inheritdoc}
     * @throws UnexpectedResponseException
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        list($responseName, $tubeName) = explode(' ', $responseLine, 2);

        if ($responseName !== Response::RESPONSE_USING) {
            throw new UnexpectedResponseException($responseName, $this, $server);
        }

        return new Response($responseName, $tubeName, $server);
    }
}
