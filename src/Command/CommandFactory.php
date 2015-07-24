<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command\CommandLineCreator\CommandLineCreator;
use Beanie\Command\CommandLineCreator\GenericCommandLineCreator;
use Beanie\Command\CommandLineCreator\PutCommandLineCreator;
use Beanie\Command\CommandLineCreator\TubeNameCheckingCommandLineCreator;
use Beanie\Command\ResponseParser\GenericResponseParser;
use Beanie\Command\ResponseParser\JobResponseParser;
use Beanie\Command\ResponseParser\ResponseParser;
use Beanie\Command\ResponseParser\SimpleValueResponseParser;
use Beanie\Command\ResponseParser\YAMLResponseParser;
use Beanie\Exception\DeadlineSoonException;
use Beanie\Exception\DrainingException;
use Beanie\Exception\ExpectedCRLFException;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Exception\JobTooBigException;
use Beanie\Exception\NotFoundException;
use Beanie\Exception\NotIgnoredException;
use Beanie\Exception\TimedOutException;

class CommandFactory
{
    protected static $defaultCommandStructure = [
        'responseParser' => GenericResponseParser::class,
        'acceptableResponses' => [],
        'expectedErrorResponses' => [],
        'commandLineCreator' => GenericCommandLineCreator::class,
        'argumentDefaults' => []
    ];

    protected static $commandStructureMap = [
        Command::COMMAND_BURY => [
            'acceptableResponses' => [Response::RESPONSE_BURIED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null,
                'priority' => Beanie::DEFAULT_PRIORITY
            ]
        ],
        Command::COMMAND_DELETE => [
            'acceptableResponses' => [Response::RESPONSE_DELETED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        Command::COMMAND_IGNORE => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_WATCHING],
            'expectedErrorResponses' => [Response::FAILURE_NOT_IGNORED],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        Command::COMMAND_KICK => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_KICKED],
            'argumentDefaults' => [
                'howMany' => Beanie::DEFAULT_MAX_TO_KICK
            ]
        ],
        Command::COMMAND_KICK_JOB => [
            'acceptableResponses' => [Response::RESPONSE_KICKED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        Command::COMMAND_LIST_TUBES => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        Command::COMMAND_LIST_TUBES_WATCHED => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        Command::COMMAND_LIST_TUBE_USED => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_USING]
        ],
        Command::COMMAND_PAUSE_TUBE => [
            'acceptableResponses' => [Response::RESPONSE_PAUSED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null,
                'howLong' => Beanie::DEFAULT_DELAY
            ]
        ],
        Command::COMMAND_PEEK => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        Command::COMMAND_PEEK_BURIED => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        Command::COMMAND_PEEK_DELAYED => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        Command::COMMAND_PEEK_READY => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        Command::COMMAND_PUT => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' =>  [Response::RESPONSE_INSERTED, Response::RESPONSE_BURIED],
            'expectedErrorResponses' => [
                Response::FAILURE_DRAINING,
                Response::FAILURE_EXPECTED_CRLF,
                Response::FAILURE_JOB_TOO_BIG
            ],
            'commandLineCreator' => PutCommandLineCreator::class,
            'argumentDefaults' => [
                'priority' => Beanie::DEFAULT_PRIORITY,
                'delay' => Beanie::DEFAULT_DELAY,
                'timeToRun' => Beanie::DEFAULT_TIME_TO_RUN
            ]
        ],
        Command::COMMAND_QUIT => [],
        Command::COMMAND_RELEASE => [
            'acceptableResponses' => [Response::RESPONSE_BURIED, Response::RESPONSE_RELEASED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null,
                'priority' => Beanie::DEFAULT_PRIORITY,
                'delay' => Beanie::DEFAULT_DELAY
            ]
        ],
        Command::COMMAND_RESERVE => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_RESERVED],
            'expectedErrorResponses' => [Response::FAILURE_DEADLINE_SOON]
        ],
        Command::COMMAND_RESERVE_WITH_TIMEOUT => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_RESERVED],
            'expectedErrorResponses' => [Response::FAILURE_DEADLINE_SOON, Response::FAILURE_TIMED_OUT],
            'argumentDefaults' => [
                'timeout' => null
            ]
        ],
        Command::COMMAND_STATS => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        Command::COMMAND_STATS_JOB => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        Command::COMMAND_STATS_TUBE => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        Command::COMMAND_TOUCH => [
            'acceptableResponses' => [Response::RESPONSE_TOUCHED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        Command::COMMAND_USE => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_USING],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        Command::COMMAND_WATCH => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_WATCHING],
            'expectedErrorResponses' => [],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ]
    ];

    protected static $errorResponseExceptionMap = [
        Response::FAILURE_NOT_FOUND => NotFoundException::class,
        Response::FAILURE_NOT_IGNORED => NotIgnoredException::class,
        Response::FAILURE_DRAINING => DrainingException::class,
        Response::FAILURE_EXPECTED_CRLF => ExpectedCRLFException::class,
        Response::FAILURE_JOB_TOO_BIG => JobTooBigException::class,
        Response::FAILURE_DEADLINE_SOON => DeadlineSoonException::class,
        Response::FAILURE_TIMED_OUT => TimedOutException::class
    ];

    /**
     * @param string $commandName
     * @param array $arguments
     * @return GenericCommand
     * @throws InvalidArgumentException
     */
    public function createCommand($commandName, array $arguments = [])
    {
        if (!isset(self::$commandStructureMap[$commandName])) {
            throw new InvalidArgumentException('Could not create Command for \'' . $commandName . '\'');
        }

        $commandStructure = array_merge(self::$defaultCommandStructure, self::$commandStructureMap[$commandName]);

        return new GenericCommand(
            $this->createCommandLineCreator($commandStructure, $commandName, $arguments),
            $this->createResponseParser($commandStructure)
        );
    }

    /**
     * @param array $commandStructure
     * @return ResponseParser
     */
    private function createResponseParser($commandStructure)
    {
        return new $commandStructure['responseParser'](
            $commandStructure['acceptableResponses'],
            array_intersect_key(
                self::$errorResponseExceptionMap,
                array_flip($commandStructure['expectedErrorResponses'])
            )
        );
    }

    /**
     * @param array $commandStructure
     * @param string $commandName
     * @param array $arguments
     * @return CommandLineCreator
     */
    private function createCommandLineCreator($commandStructure, $commandName, $arguments)
    {
        return new $commandStructure['commandLineCreator'](
            $commandName, $arguments, $commandStructure['argumentDefaults']
        );
    }
}
