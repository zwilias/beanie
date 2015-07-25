<?php


namespace Beanie\Job;


use Beanie\Command\Command;
use Beanie\Command\Response;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Exception\NotFoundException;
use Beanie\Server\Server;
use Beanie\Util\FactoryTrait;

class JobFactory
{
    use FactoryTrait;

    private static $responseToStateMap = [
        Response::RESPONSE_RESERVED => Job::STATE_RESERVED,
        Response::RESPONSE_INSERTED => Job::STATE_RELEASED,
        Response::RESPONSE_RELEASED => Job::STATE_RELEASED,
        Response::RESPONSE_BURIED => Job::STATE_BURIED
    ];

    /**
     * @param \Beanie\Command\Response $response
     * @return Job
     * @throws InvalidArgumentException
     */
    public function createFromResponse(Response $response)
    {
        $state = isset(self::$responseToStateMap[$response->getName()])
            ? self::$responseToStateMap[$response->getName()]
            : Job::STATE_UNKNOWN;

        $this->validateResponseData($response->getData());

        return new Job($response->getData()['id'], $response->getData()['data'], $response->getServer(), $state);
    }

    /**
     * @param Command $command
     * @param Server $server
     * @return Job|null
     */
    public function createFromCommand(Command $command, Server $server)
    {
        try {
            return $this->createFromResponse($server->dispatchCommand($command));
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function validateResponseData(array $data)
    {
        if (!(
            isset($data['id']) &&
            isset($data['data'])
        )) {
            throw new InvalidArgumentException('Could not create Job from response: incorrect data returned');
        }
    }
}
