<?php


namespace Beanie\Command;


use Beanie\Command;
use Beanie\Exception;
use Beanie\Exception\DeadlineSoonException;
use Beanie\Exception\TimedOutException;
use Beanie\Response;
use Beanie\Server\Server;

class ReserveCommand extends AbstractCommand
{
    const MODE_RESERVE = Command::COMMAND_RESERVE;
    const MODE_RESERVE_WITH_TIMEOUT = Command::COMMAND_RESERVE_WITH_TIMEOUT;

    /** @var string */
    protected $mode;

    /** @var int */
    protected $timeout = 0;

    /**
     * @param string $mode
     * @param int $timeout
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($mode = self::MODE_RESERVE, $timeout = 0)
    {
        $validModes = [self::MODE_RESERVE, self::MODE_RESERVE_WITH_TIMEOUT];
        if (!in_array($mode, $validModes)) {
            throw new Exception\InvalidArgumentException('Can\'t reserve with mode: \'' . $mode . '\'');
        }

        $this->mode = $mode;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     * @throws \Beanie\Exception\DeadlineSoonException
     * @throws \Beanie\Exception\TimedOutException
     */
    protected function parseResponseLine($responseLine, Server $server)
    {
        switch ($responseLine) {
            case Response::FAILURE_DEADLINE_SOON:
                throw new DeadlineSoonException($this, $server);
            case Response::FAILURE_TIMED_OUT:
                throw new TimedOutException($this, $server);

        }

        list(, $jobId, $dataLength) = explode(' ', $responseLine, 3);
        return new Response(Response::RESPONSE_RESERVED, [
            'id' => $jobId,
            'data' => $server->readData($dataLength)
        ], $server);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        if ($this->mode == self::MODE_RESERVE_WITH_TIMEOUT) {
            return sprintf('%s %s', Command::COMMAND_RESERVE_WITH_TIMEOUT, $this->timeout);
        } else {
            return Command::COMMAND_RESERVE;
        }
    }
}
