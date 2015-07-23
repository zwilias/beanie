<?php


namespace Beanie\Server;


use Beanie\Beanie;
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
    const TRANSFORM_USE = 1;
    const TRANSFORM_WATCHED = 2;
    const TRANSFORM_BOTH = 3;

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
     * @param int $mode
     * @return \Beanie\Command\AbstractCommand[]
     */
    public function calculateTransformationTo(TubeStatus $goal, $mode = self::TRANSFORM_BOTH)
    {
        $commands = [];

        if ($mode & self::TRANSFORM_WATCHED) {
            $commands = $this->calculateTransformWatched($goal->getWatchedTubes());
        }

        if (
            $mode & self::TRANSFORM_USE &&
            $goal->getCurrentTube() !== $this->currentTube
        ) {
            $commands[] = new UseCommand($goal->getCurrentTube());
        }

        return $commands;
    }

    /**
     * @param string[] $otherWatchedTubes
     * @return \Beanie\Command\AbstractCommand[]
     */
    protected function calculateTransformWatched(array $otherWatchedTubes = [])
    {
        $commands = [];

        foreach (array_diff($otherWatchedTubes, $this->getWatchedTubes()) as $watchTube) {
            $commands[] = new WatchCommand($watchTube);
        }

        foreach (array_diff($this->getWatchedTubes(), $otherWatchedTubes) as $ignoreTube) {
            $commands[] = new IgnoreCommand($ignoreTube);
        }

        return $commands;
    }
}
