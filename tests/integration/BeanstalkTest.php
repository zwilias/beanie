<?php


/**
 * Class BeanstalkTest
 * @coversNothing
 */
class BeanstalkTest extends PHPUnit_Framework_TestCase
{
    /** @var \Beanie\Beanie */
    protected $beanie;

    /** @var string */
    protected $serverName;

    public function setUp()
    {
        $host = defined('BEANSTALK_HOST')
            ? BEANSTALK_HOST
            : 'localhost';
        $port = defined('BEANSTALK_PORT')
            ? BEANSTALK_PORT
            : 11300;

        $this->serverName = sprintf('%s:%s', $host, $port);

        $this->beanie = \Beanie\Beanie::pool([$this->serverName]);
    }

    public function tearDown()
    {
        $this->beanie = null;
    }

    public function testProducer_canProduce()
    {
        $jobData = 'test';
        $producer = $this->beanie->producer();


        $job = $producer->put($jobData);


        $this->assertInstanceOf(\Beanie\Job\Job::class, $job);
        $this->assertEquals($jobData, $job->getData());

        $job->delete();
    }

    public function testProducer_putsJobInCorrectTube()
    {
        $jobData = 'test';
        $producer = $this->beanie->producer();


        $producer->useTube('test');
        $job = $producer->put($jobData);
        $stats = $job->stats();


        $this->assertEquals('test', $stats['tube']);

        $job->delete();
    }

    /**
     * @expectedException \Beanie\Exception\NotFoundException
     * @expectedExceptionCode 404
     */
    public function testDeletedJob_noLongerExists()
    {
        $jobData = 'test';
        $producer = $this->beanie->producer();


        $job = $producer->put($jobData);
        $job->delete();


        $job->stats();
    }

    public function testWorker_reserveWithTimeout_returnsNull()
    {
        $worker = $this->beanie->worker();
        $worker->watch('test', true);


        $job = $worker->reserve(0);


        $this->assertNull($job);
    }

    public function testWorker_reserve_retrievesJob()
    {
        $this->beanie->producer()->useTube('test')->put('testjob');


        $job = $this->beanie->worker()->watch('test')->reserve();


        $this->assertInstanceOf(\Beanie\Job\Job::class, $job);
        $this->assertEquals('testjob', $job->getData());
        $this->assertNotNull($job->getId());
        $this->assertEquals(\Beanie\Job\Job::STATE_RESERVED, $job->getState());

        $job->delete();
    }

    public function testManager_listsTubes()
    {
        $tubes = $this->beanie->manager($this->serverName)->tubes();


        $this->assertGreaterThan(0, count($tubes));

        $found = false;
        foreach ($tubes as $tube) {
            $stats = $tube->stats();
            if ($stats['name'] == 'default') {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->fail('Excepted to find default tube, did not find');
        }
    }

    public function testJob_buryBuries_kickKicks()
    {
        $this->beanie->producer()->put('test');
        $job = $this->beanie->worker()->reserve();


        $job->bury();
        $jobStats = $job->stats();

        $this->assertEquals($jobStats['state'], 'buried');


        $job->kick();
        $jobStats = $job->stats();

        $this->assertEquals($jobStats['state'], 'ready');
        $job->delete();
    }
}
