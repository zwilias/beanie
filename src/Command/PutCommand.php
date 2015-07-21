<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Exception\DrainingException;
use Beanie\Exception\ExpectedCRLFException;
use Beanie\Exception\JobTooBigException;
use Beanie\Response;
use Beanie\Server\Server;

class PutCommand extends AbstractCommand
{
    /** @var int */
    protected $_priority;

    /** @var int */
    protected $_delay;

    /** @var int */
    protected $_timeToRun;

    /** @var string */
    protected $_data;

    public function __construct(
        $data,
        $priority = Beanie::DEFAULT_PRIORITY,
        $delay = Beanie::DEFAULT_DELAY,
        $timeToRun = Beanie::DEFAULT_TIME_TO_RUN
    )
    {
        $this->_data = $data;
        $this->_priority = (int) $priority;
        $this->_delay = (int) $delay;
        $this->_timeToRun = (int) $timeToRun;
    }

    /**
     * @inheritdoc
     * @throws DrainingException
     * @throws ExpectedCRLFException
     * @throws JobTooBigException
     */
    protected function _parseResponse($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_DRAINING:
                throw new DrainingException($server);
            case Response::FAILURE_EXPECTED_CRLF:
                throw new ExpectedCRLFException($server);
            case Response::FAILURE_JOB_TOO_BIG:
                throw new JobTooBigException($server);
        }

        list($responseName, $responseData) = explode(' ', $responseLine, 2);

        return new Response($responseName, $responseData, $server);
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return join(' ', [
            Command::COMMAND_PUT,
            $this->_priority,
            $this->_delay,
            $this->_timeToRun,
            strlen($this->getData())
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->_data;
    }
}
