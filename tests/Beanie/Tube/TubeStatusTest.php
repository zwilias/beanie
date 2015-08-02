<?php


namespace Beanie\Tube;


use Beanie\Beanie;
use Beanie\Command\CommandFactory;
use Beanie\Command\CommandInterface;

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
     * @param int $mode
     * @param CommandInterface[] $expectedCommands
     * @dataProvider transformCommandsProvider
     */
    public function testGetCommandsToTransformTo($useTube, $watchTubes, $otherUseTube, $otherWatchTubes, $mode, $expectedCommands)
    {
        $tubeStatus = (new TubeStatus())
            ->setCurrentTube($useTube)
            ->setWatchedTubes($watchTubes)
        ;

        $otherTubeStatus = (new TubeStatus())
            ->setCurrentTube($otherUseTube)
            ->setWatchedTubes($otherWatchTubes)
        ;


        $actualCommands = $tubeStatus->calculateTransformationTo($otherTubeStatus, $mode);


        $expectedCommandLines = array_map(
            function (CommandInterface $command) { return $command->getCommandLine(); },
            $expectedCommands
        );
        sort($expectedCommandLines);

        $actualCommandLines = array_map(
            function (CommandInterface $command) { return $command->getCommandLine(); },
            $actualCommands
        );
        sort($actualCommandLines);

        $this->assertEquals($expectedCommandLines, $actualCommandLines);
    }

    public function transformCommandsProvider()
    {
        $commandFactory = CommandFactory::instance();

        return [
            'identical' => [
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                TubeStatus::TRANSFORM_BOTH,
                []
            ],
            'overlapping-watch' => [
                Beanie::DEFAULT_TUBE,
                ['1only1', '1only2', 'shared1', 'shared2', '1only3'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_BOTH,
                [
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only1']),
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only2']),
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only3']),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only1']),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only2'])
                ]
            ],
            'only-additions' => [
                Beanie::DEFAULT_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_BOTH,
                [
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only1']),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only2'])
                ]
            ],
            'only-removals' => [
                Beanie::DEFAULT_TUBE,
                ['1only1', '1only2', 'shared1', 'shared2', '1only3'],
                Beanie::DEFAULT_TUBE,
                ['shared1', 'shared2'],
                TubeStatus::TRANSFORM_BOTH,
                [
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only1']),
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only2']),
                    $commandFactory->create(CommandInterface::COMMAND_IGNORE, ['1only3'])
                ]
            ],
            'different-use' => [
                self::TEST_TUBE,
                [Beanie::DEFAULT_TUBE],
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                TubeStatus::TRANSFORM_BOTH,
                [
                    $commandFactory->create(CommandInterface::COMMAND_USE, [Beanie::DEFAULT_TUBE])
                ]
            ],
            'different-use-ignored' => [
                self::TEST_TUBE,
                [Beanie::DEFAULT_TUBE],
                Beanie::DEFAULT_TUBE,
                [Beanie::DEFAULT_TUBE],
                TubeStatus::TRANSFORM_WATCHED,
                []
            ],
            'different-watch-ignored' => [
                Beanie::DEFAULT_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_USE,
                []
            ],
            'all-different' => [
                self::TEST_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_BOTH,
                [
                    $commandFactory->create(CommandInterface::COMMAND_USE, [Beanie::DEFAULT_TUBE]),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only1']),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only2'])
                ]
            ],
            'all-different-only-use' => [
                self::TEST_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_USE,
                [
                    $commandFactory->create(CommandInterface::COMMAND_USE, [Beanie::DEFAULT_TUBE])
                ]
            ],
            'all-different-only-watched' => [
                self::TEST_TUBE,
                ['shared1', 'shared2'],
                Beanie::DEFAULT_TUBE,
                ['2only1', 'shared1', 'shared2', '2only2'],
                TubeStatus::TRANSFORM_WATCHED,
                [
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only1']),
                    $commandFactory->create(CommandInterface::COMMAND_WATCH, ['2only2'])
                ]
            ]
        ];
    }
}
