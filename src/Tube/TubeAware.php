<?php


namespace Beanie\Tube;




interface TubeAware
{
    /**
     * @return TubeStatus
     */
    public function getTubeStatus();

    /**
     * @param TubeStatus $tubeStatus
     * @param int $mode One of TubeStatus::TRANSFORM_USE, TubeStatus::TRANSFORM_WATCHED or TubeStatus::TRANSFORM_BOTH
     * @return $this
     */
    public function transformTubeStatusTo(TubeStatus $tubeStatus, $mode);
}
