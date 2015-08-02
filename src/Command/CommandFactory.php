<?php


namespace Beanie\Command;


use Beanie\Beanie;
use Beanie\Command\CommandLineCreator\CommandLineCreatorInterface;
use Beanie\Command\CommandLineCreator\GenericCommandLineCreator;
use Beanie\Command\CommandLineCreator\PutCommandLineCreator;
use Beanie\Command\CommandLineCreator\TubeNameCheckingCommandLineCreator;
use Beanie\Command\ResponseParser\GenericResponseParser;
use Beanie\Command\ResponseParser\JobResponseParser;
use Beanie\Command\ResponseParser\ResponseParserInterface;
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
use Beanie\Util\FactoryInterface;
use Beanie\Util\FactoryTrait;

class CommandFactory implements FactoryInterface
{
    use FactoryTrait;

    protected static $defaultCommandStructure = [
        'responseParser' => GenericResponseParser::class,
        'acceptableResponses' => [],
        'expectedErrorResponses' => [],
        'commandLineCreator' => GenericCommandLineCreator::class,
        'argumentDefaults' => []
    ];

    protected static $commandStructureMap = [
        CommandInterface::COMMAND_BURY => [
            'acceptableResponses' => [Response::RESPONSE_BURIED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null,
                'priority' => Beanie::DEFAULT_PRIORITY
            ]
        ],
        CommandInterface::COMMAND_DELETE => [
            'acceptableResponses' => [Response::RESPONSE_DELETED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        CommandInterface::COMMAND_IGNORE => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_WATCHING],
            'expectedErrorResponses' => [Response::FAILURE_NOT_IGNORED],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        CommandInterface::COMMAND_KICK => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_KICKED],
            'argumentDefaults' => [
                'howMany' => Beanie::DEFAULT_MAX_TO_KICK
            ]
        ],
        CommandInterface::COMMAND_KICK_JOB => [
            'acceptableResponses' => [Response::RESPONSE_KICKED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        CommandInterface::COMMAND_LIST_TUBES => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        CommandInterface::COMMAND_LIST_TUBES_WATCHED => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        CommandInterface::COMMAND_LIST_TUBE_USED => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_USING]
        ],
        CommandInterface::COMMAND_PAUSE_TUBE => [
            'acceptableResponses' => [Response::RESPONSE_PAUSED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null,
                'howLong' => Beanie::DEFAULT_DELAY
            ]
        ],
        CommandInterface::COMMAND_PEEK => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        CommandInterface::COMMAND_PEEK_BURIED => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        CommandInterface::COMMAND_PEEK_DELAYED => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        CommandInterface::COMMAND_PEEK_READY => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_FOUND],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND]
        ],
        CommandInterface::COMMAND_PUT => [
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
        CommandInterface::COMMAND_QUIT => [],
        CommandInterface::COMMAND_RELEASE => [
            'acceptableResponses' => [Response::RESPONSE_BURIED, Response::RESPONSE_RELEASED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null,
                'priority' => Beanie::DEFAULT_PRIORITY,
                'delay' => Beanie::DEFAULT_DELAY
            ]
        ],
        CommandInterface::COMMAND_RESERVE => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_RESERVED],
            'expectedErrorResponses' => [Response::FAILURE_DEADLINE_SOON]
        ],
        CommandInterface::COMMAND_RESERVE_WITH_TIMEOUT => [
            'responseParser' => JobResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_RESERVED],
            'expectedErrorResponses' => [Response::FAILURE_DEADLINE_SOON, Response::FAILURE_TIMED_OUT],
            'argumentDefaults' => [
                'timeout' => null
            ]
        ],
        CommandInterface::COMMAND_STATS => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK]
        ],
        CommandInterface::COMMAND_STATS_JOB => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        CommandInterface::COMMAND_STATS_TUBE => [
            'responseParser' => YAMLResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_OK],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        CommandInterface::COMMAND_TOUCH => [
            'acceptableResponses' => [Response::RESPONSE_TOUCHED],
            'expectedErrorResponses' => [Response::FAILURE_NOT_FOUND],
            'argumentDefaults' => [
                'jobId' => null
            ]
        ],
        CommandInterface::COMMAND_USE => [
            'responseParser' => SimpleValueResponseParser::class,
            'acceptableResponses' => [Response::RESPONSE_USING],
            'commandLineCreator' => TubeNameCheckingCommandLineCreator::class,
            'argumentDefaults' => [
                'tubeName' => null
            ]
        ],
        CommandInterface::COMMAND_WATCH => [
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
    public function create($commandName, array $arguments = [])
    {
        if (!isset(self::$commandStructureMap[$commandName])) {
            throw new InvalidArgumentException('Could not create Command for \'' . $commandName . '\'');
        }

        $commandStructure = array_merge(self::$defaultCommandStructure, self::$commandStructureMap[$commandName]);

        return new GenericCommand(
            $this->createLineCreator($commandStructure, $commandName, $arguments),
            $this->createResponseParser($commandStructure)
        );
    }

    /**
     * @param array $commandStructure
     * @return ResponseParserInterface
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
     * @return CommandLineCreatorInterface
     */
    private function createLineCreator($commandStructure, $commandName, $arguments)
    {
        return new $commandStructure['commandLineCreator'](
            $commandName, $arguments, $commandStructure['argumentDefaults']
        );
    }
}
