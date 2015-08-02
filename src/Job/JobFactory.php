<?php


namespace Beanie\Job;


use Beanie\Command\CommandInterface;
use Beanie\Command\Response;
use Beanie\Exception\InvalidArgumentException;
use Beanie\Server\Server;
use Beanie\Util\FactoryInterface;
use Beanie\Util\FactoryTrait;

class JobFactory implements FactoryInterface
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
     * @param CommandInterface $command
     * @param Server $server
     * @return JobOath
     */
    public function createFromCommand(CommandInterface $command, Server $server)
    {
        return new JobOath(
            $server->dispatchCommand($command),
            $this
        );
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
