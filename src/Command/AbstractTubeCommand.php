<?php


namespace Beanie\Command;


use Beanie\Exception\UnexpectedResponseException;
use Beanie\Response;
use Beanie\Server\Server;

abstract class AbstractTubeCommand extends AbstractCommand
{
    protected $_tubeName;

    public function __construct($tubeName)
    {
        $this->_ensureValidName($tubeName);
        $this->_tubeName = $tubeName;
    }

    /**
     * @return string
     */
    abstract protected function _getExpectedResponseName();

    /**
     * @return string
     */
    abstract protected function _getCommandName();

    /**
     * @inheritDoc
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        list($responseName, $responseValue) = explode(' ', $responseLine, 2);
        $expectedResponseName = $this->_getExpectedResponseName();

        if ($responseName !== $expectedResponseName) {
            throw new UnexpectedResponseException($responseName, $this, $server);
        }

        return new Response($expectedResponseName, $responseValue, $server);
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', $this->_getCommandName(), $this->_tubeName);
    }

}
