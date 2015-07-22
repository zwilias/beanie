<?php


namespace Beanie\Server;


use Beanie\Beanie;
use Beanie\Command;
use Beanie\Command\AbstractCommand;
use Beanie\Command\IgnoreCommand;
use Beanie\Command\WatchCommand;

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


    /**
     * @param string $useTube
     * @param string[] $watchTubes
     * @param string $otherUseTube
     * @param string[] $otherWatchTubes
     * @param AbstractCommand[] $expectedCommands
     * @dataProvider transformCommandsProvider
     */
    public function testGetCommandsToTransformTo($useTube, $watchTubes, $otherUseTube, $otherWatchTubes, $expectedCommands)
    {
        $tubeStatus = (new TubeStatus())
            ->setCurrentTube($useTube)
            ->setWatchedTubes($watchTubes)
        ;

        $otherTubeStatus = (new TubeStatus())
            ->setCurrentTube($otherUseTube)
            ->setWatchedTubes($otherWatchTubes)
        ;


        $actualCommands = $tubeStatus->getCommandsToTransformTo($otherTubeStatus);


        $expectedCommandLines = array_map(
            function (Command $command) { return $command->getCommandLine(); },
            $expectedCommands
        );
        sort($expectedCommandLines);

        $actualCommandLines = array_map(
            function (Command $command) { return $command->getCommandLine(); },
            $actualCommands
        );
        sort($actualCommandLines);

        $this->assertEquals($expectedCommandLines, $actualCommandLines);
    }

    public function transformCommandsProvider()
    {
        return [
            'identical' => [
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                []
            ],
            'overlapping-watch' => [
                Beanie::DEFAULT_TUBE,
                ['1only1', '1only2', 'shared1', 'shared2', '1only3'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                [
                    new IgnoreCommand('1only1'),
                    new IgnoreCommand('1only2'),
                    new IgnoreCommand('1only3'),
                    new WatchCommand('2only1'),
                    new WatchCommand('2only2')
                ]
            ],
            'only-additions' => [
                Beanie::DEFAULT_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                [
                    new WatchCommand('2only1'),
                    new WatchCommand('2only2')
                ]
            ],
            'only-removals' => [
                Beanie::DEFAULT_TUBE,
                ['1only1', '1only2', 'shared1', 'shared2', '1only3'],
                Beanie::DEFAULT_TUBE,
                ['shared1', 'shared2'],
                [
                    new IgnoreCommand('1only1'),
                    new IgnoreCommand('1only2'),
                    new IgnoreCommand('1only3')
                ]
            ],
            'different-use' => [
                self::TEST_TUBE,
                [Beanie::DEFAULT_TUBE],
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                [
                    new Command\UseCommand(Beanie::DEFAULT_TUBE)
                ]
            ]
        ];
    }
}
