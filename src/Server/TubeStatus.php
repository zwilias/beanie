<?php


namespace Beanie\Server;


use Beanie\Beanie;
use Beanie\Command\AbstractCommand;
use Beanie\Command\IgnoreCommand;
use Beanie\Command\UseCommand;
use Beanie\Command\WatchCommand;

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

    /**
     * @param string[] $tubes
     * @return $this
     */
    public function setWatchedTubes(array $tubes)
    {
        $this->watchedTubes = $tubes;
        return $this;
    }

    /**
     * @param TubeStatus $goal
     * @return AbstractCommand[]
     */
    public function getCommandsToTransformTo(TubeStatus $goal)
    {
        $commands = [];

        if ($goal->getCurrentTube() !== $this->currentTube) {
            $commands[] = new UseCommand($goal->getCurrentTube());
        }

        foreach (array_diff($goal->getWatchedTubes(), $this->getWatchedTubes()) as $watchTube)
        {
            $commands[] = new WatchCommand($watchTube);
        }

        foreach (array_diff($this->getWatchedTubes(), $goal->getWatchedTubes()) as $ignoreTube)
        {
            $commands[] = new IgnoreCommand($ignoreTube);
        }

        return $commands;
    }
}
