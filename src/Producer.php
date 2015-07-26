<?php


namespace Beanie;


use Beanie\Command\Command;
use Beanie\Command\CommandFactory;
use Beanie\Command\Response;
use Beanie\Job\Job;
use Beanie\Server\Pool;
use Beanie\Server\TubeAwareTrait;
use Beanie\Tube\TubeAware;
use Beanie\Tube\TubeStatus;

class Producer implements TubeAware
{
    use TubeAwareTrait;

    /** @var Pool */
    protected $pool;

    /** @var CommandFactory */
    protected $commandFactory;

    /** @var array */
    protected static $jobStateMap = [
        Response::RESPONSE_INSERTED => Job::STATE_RELEASED,
        Response::RESPONSE_BURIED => Job::STATE_BURIED
    ];

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
        $this->tubeStatus = new TubeStatus();
        $this->commandFactory = CommandFactory::instance();
    }

    /**
     * @param string $jobData
     * @param int $priority
     * @param int $delay
     * @param int $timeToRun
     * @return Job
     * @throws Exception\InvalidArgumentException
     */
    public function put(
        $jobData,
        $priority = Beanie::DEFAULT_PRIORITY,
        $delay = Beanie::DEFAULT_DELAY,
        $timeToRun = Beanie::DEFAULT_TIME_TO_RUN
    ) {
        return $this->createJob(
            $jobData,
            $this->pool->transformTubeStatusTo($this->tubeStatus)->dispatchCommand(
                $this->commandFactory->create(Command::COMMAND_PUT, [
                    $jobData, $priority, $delay, $timeToRun
                ])
            )
        );
    }

    /**
     * @param string $jobData
     * @param Response $response
     * @return Job
     */
    protected function createJob($jobData, Response $response)
    {
        return new Job(
            $response->getData(),
            $jobData,
            $response->getServer(),
            self::$jobStateMap[$response->getName()]
        );
    }

    /**
     * @param string $tubeName
     * @return $this
     */
    public function useTube($tubeName)
    {
        $this->tubeStatus->setCurrentTube($tubeName);
        return $this;
    }

    /**
     * @return Pool
     */
    public function getPool()
    {
        return $this->pool;
    }
}
