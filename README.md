# Beanie
> A clean, lean PHP beanstalk client

[![Build Status](https://travis-ci.org/zwilias/Beanie.svg?branch=master)](https://travis-ci.org/zwilias/Beanie)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zwilias/Beanie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zwilias/Beanie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/zwilias/Beanie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/zwilias/Beanie/?branch=master)

## Core features

- Support for connection pools
- Clean distinction between concerns: Producers, Workers and Managers
- Clean interface. Want to touch a Job? `$job->touch();`
- Full support for the beanstalk protocol [as documented][protocol] at the time of writing

[protocol]: https://github.com/kr/beanstalkd/blob/master/doc/protocol.md

## Quickstart

### Producer

```php
use Beanie\Beanie;

// create a Producer for the pool
$producer = Beanie::pool(['localhost:11300', 'otherhost:11301'])->producer();

// tell the producer all jobs created should go to a certain tube
$producer->use('some-tube');

// put the job on a random connection in the pool
$job = $producer->put('some job data');
print_r($job->stats());
```

### Worker

```php
use Beanie\Beanie;

// get a Worker for a named connection in the pool
$worker = Beanie::pool(['localhost:11300', 'otherhost:11301'])->worker('otherhost:11301');

// tell the Worker to add a tube to the watchlist
$worker->watch('some-tube');

// now let's get ourselves some work to do
$job = $worker->reserve();

echo $job->getData();

$job->delete();
```

### Manager

```php
use Beanie\Beanie;

// get a Manager instance for each connection in the pool
$managers = Beanie::pool(['localhost:11300', 'otherhost:11301'])->managers();

// print stats for each connection in the pool
foreach ($managers as $manager) {
    print_r($manager->stats());
}
```
