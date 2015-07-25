<?php


namespace Beanie\Command\CommandLineCreator;


class PutCommandLineCreatorTest extends \PHPUnit_Framework_TestCase
{
    const TEST_COMMAND = 'test';

    /**
     * @param $params
     * @dataProvider putCommandParams
     */
    public function testCreate_withData_setsDataAndLength(
        $params
    ) {
        $paramsCopy = array_slice($params, 0);
        $data = array_shift($paramsCopy);
        array_push($paramsCopy, strlen($data));

        $expected = sprintf('%s %s', self::TEST_COMMAND, join(' ', $paramsCopy));


        $commandLineCreator = new PutCommandLineCreator(self::TEST_COMMAND, $params);
        $commandLine = $commandLineCreator->getCommandLine();


        $this->assertEquals($expected, $commandLine);
        $this->assertTrue($commandLineCreator->hasData());
        $this->assertEquals($data, $commandLineCreator->getData());
    }

    public function putCommandParams()
    {
        return [
            [['this is some data']],
            [['more data', 1, 2, 3]]
        ];
    }
}
