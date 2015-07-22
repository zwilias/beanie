<?php


namespace Beanie\Server;


use Beanie\Beanie;

/**
 * Class TubeStatus
 *
 * Keeps track of the currently used and watched tubes.
 *
 * @package Beanie\Server
 */
class TubeStatus
{
    /** @var string */
    protected $currentTube = Beanie::DEFAULT_TUBE;

    /** @var string[] */
    protected $watchedTubes = [Beanie::DEFAULT_TUBE];


    /**
     * @param string $tubeName
     * @return $this
     */
    public function setCurrentTube($tubeName)
    {
        $this->currentTube = $tubeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentTube()
    {
        return $this->currentTube;
    }

    /**
     * @param string $tubeName
     * @return $this
     */
    public function addWatchedTube($tubeName)
    {
        if (!array_search($tubeName, $this->watchedTubes)) {
            $this->watchedTubes[] = $tubeName;
        }

        return $this;
    }

    /**
     * @param string $tubeName
     * @return $this
     */
    public function removeWatchedTube($tubeName)
    {
        if (($position = array_search($tubeName, $this->watchedTubes)) !== false) {
            array_splice($this->watchedTubes, $position, 1);
        }
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getWatchedTubes()
    {
        return $this->watchedTubes;
    }
}
