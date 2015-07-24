<?php


namespace Beanie\Command;




class PeekCommand extends AbstractPeeKCommand
{
    /** @var int */
    protected $jobId;

    /**
     * @param int $jobId
     */
    public function __construct($jobId)
    {
        $this->jobId = (int) $jobId;
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine()
    {
        return sprintf('%s %s', Command::COMMAND_PEEK, $this->jobId);
    }
}
