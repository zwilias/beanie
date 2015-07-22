<?php


namespace Beanie\Server;


use Beanie\Beanie;

class TubeStatusTest extends \PHPUnit_Framework_TestCase
{
    const TEST_TUBE = 'test-tube';

    public function testCreate_usesDefaultTube()
    {
        $this->assertEquals(Beanie::DEFAULT_TUBE, (new TubeStatus())->getCurrentTube());
    }

    public function testCreate_usesDefaultWatchList()
    {
        $this->assertEquals([Beanie::DEFAULT_TUBE], (new TubeStatus())->getWatchedTubes());
    }

    public function testSetCurrentTube_updatesCurrentTube()
    {
        $tubeStatus = new TubeStatus();


        $tubeStatus->setCurrentTube(self::TEST_TUBE);


        $this->assertEquals(self::TEST_TUBE, $tubeStatus->getCurrentTube());
    }

    public function testAddWatchedTube_addsWatchedTube()
    {
        $tubeStatus = new TubeStatus();


        $tubeStatus->addWatchedTube(self::TEST_TUBE);


        $this->assertEquals([Beanie::DEFAULT_TUBE, self::TEST_TUBE], $tubeStatus->getWatchedTubes());
    }

    public function testRemoveWatchedTube_removesWatchedTube()
    {
        $tubeStatus = new TubeStatus();


        $tubeStatus->addWatchedTube(self::TEST_TUBE);
        $tubeStatus->removeWatchedTube(Beanie::DEFAULT_TUBE);


        $this->assertEquals([self::TEST_TUBE], $tubeStatus->getWatchedTubes());
    }

    public function testAddWatchedTube_noDoubles()
    {
        $tubesStatus = new TubeStatus();


        $tubesStatus->addWatchedTube(self::TEST_TUBE);
        $tubesStatus->addWatchedTube(self::TEST_TUBE);


        $this->assertEquals([Beanie::DEFAULT_TUBE, self::TEST_TUBE], $tubesStatus->getWatchedTubes());
    }

    public function testRemoveWatchTube_notFound_noIssue()
    {
        $tubeStatus = new TubeStatus();


        $tubeStatus->removeWatchedTube(self::TEST_TUBE);


        $this->assertEquals([Beanie::DEFAULT_TUBE], $tubeStatus->getWatchedTubes());
    }

    public function testRemoveWatchTube_fromMultiple()
    {
        $tubeStatus = new TubeStatus();

        $tubeStatus->addWatchedTube('tube-1');
        $tubeStatus->addWatchedTube('tube-2');
        $tubeStatus->addWatchedTube('tube-3');


        $tubeStatus->removeWatchedTube('tube-2');


        $this->assertEquals([Beanie::DEFAULT_TUBE, 'tube-1', 'tube-3'], $tubeStatus->getWatchedTubes());
    }
}
