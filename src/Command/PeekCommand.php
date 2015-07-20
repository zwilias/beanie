<?php


namespace Beanie\Command;


use Beanie\Command;

class PeekCommand extends AbstractPeeKCommand
{
    /** @var int */
    protected $_jobId;

    /**
     * @param int $jobId
     */
    public function __construct($jobId)
    {
        $this->_jobId = (int) $jobId;
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_PEEK, $this->_jobId);
    }
}
