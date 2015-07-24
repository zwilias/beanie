<?php


namespace Beanie\Server;



use Beanie\Command\AbstractCommand;
use Beanie\Command\Response;
use Beanie\Tube\TubeStatus;

trait TubeAwareTrait
{
    /**
     * It is the class exhibiting this trait that becomes responsible for setting this state.
     *
     * @var TubeStatus
     */
    protected $tubeStatus;

    /**
     * @param AbstractCommand $command
     * @return \Beanie\Command\Response
     */
    abstract public function dispatchCommand(AbstractCommand $command);

    /**
     * @return TubeStatus
     */
    public function getTubeStatus()
    {
        return $this->tubeStatus;
    }

    /**
     * @param TubeStatus $otherTube
     * @param int $mode One of the TubeStatus::TRANSFORM_* modes
     * @return static
     */
    public function transformTubeStatusTo(TubeStatus $otherTube, $mode = TubeStatus::TRANSFORM_BOTH)
    {
        foreach (
            $this->getTubeStatus()->calculateTransformationTo($otherTube, $mode) as $transformation
        ) {
            $this->dispatchCommand($transformation);
        }

        if ($mode & TubeStatus::TRANSFORM_USE) {
            $this->getTubeStatus()->setCurrentTube($otherTube->getCurrentTube());
        }

        if ($mode & TubeStatus::TRANSFORM_WATCHED) {
            $this->getTubeStatus()->setWatchedTubes($otherTube->getWatchedTubes());
        }

        return $this;
    }
}
