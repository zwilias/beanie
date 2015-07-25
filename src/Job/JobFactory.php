<?php


namespace Beanie\Job;


use Beanie\Command\Response;
use Beanie\Exception\InvalidArgumentException;

class JobFactory
{
    private static $responseToStateMap = [
        Response::RESPONSE_INSERTED => Job::STATE_RELEASED,
        Response::RESPONSE_RELEASED => Job::STATE_RELEASED,
        Response::RESPONSE_BURIED => Job::STATE_BURIED
    ];

    /** @var JobFactory */
    private static $instance;

    /**
     * @return JobFactory
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function unsetInstance()
    {
        self::$instance = null;
    }

    /**
     * @param \Beanie\Command\Response $response
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
