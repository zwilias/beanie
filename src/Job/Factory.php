<?php


namespace Beanie\Job;


use Beanie\Exception\InvalidArgumentException;
use Beanie\Job;
use Beanie\Response;

class Factory
{
    private static $responseToStateMap = [
        Response::RESPONSE_INSERTED => Job::STATE_RELEASED,
        Response::RESPONSE_RELEASED => Job::STATE_RELEASED,
        Response::RESPONSE_BURIED => Job::STATE_BURIED
    ];

    /**
     * @param Response $response
     * @return Job
     * @throws InvalidArgumentException
     */
    public function createFrom(Response $response)
    {
        $state = isset(self::$responseToStateMap[$response->getName()])
            ? self::$responseToStateMap[$response->getName()]
            : Job::STATE_UNKNOWN;

        $this->validateResponseData($response->getData());

        return new Job($response->getData()['id'], $response->getData()['data'], $response->getServer(), $state);
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
