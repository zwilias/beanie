<?php


namespace Beanie\Job;


use Beanie\Exception\NotFoundException;
use Beanie\Oath;
use Beanie\Server\ResponseOath;

class JobOath implements Oath
{
    /** @var ResponseOath */
    protected $responseOath;

    /** @var JobFactory */
    protected $jobFactory;

    /**
     * @param ResponseOath $oath
     * @param JobFactory|null $jobFactory
     */
    public function __construct(ResponseOath $oath, JobFactory $jobFactory = null)
    {
        $this->jobFactory = $jobFactory ?: JobFactory::instance();
        $this->responseOath = $oath;
    }

    /**
     * @return resource
     */
    public function getSocket()
    {
        return $this->responseOath->getSocket();
    }

    /**
     * @return Job|null
     */
    public function invoke()
    {
        try {
            return $this->jobFactory->createFromResponse($this->responseOath->invoke());
        } catch (NotFoundException $notFound) {
            return null;
        }
    }
}
