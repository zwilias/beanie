<?php


namespace Beanie\Server;



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
        $this->getTubeStatus()->transformTo($otherTube, $mode);

        return $this;
    }
}
