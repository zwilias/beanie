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

A *Producer* exposes the necessary commands to produce jobs on the queue. It operates on an entire *Pool*, and will
create its jobs on random connections from that pool, as a means of randomly distributing load to the workers.

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

A *Worker* exposes the commands needed to consume jobs from the queue. Rather than operating on the entire *Pool* - like
the *Producers* do - it only operates on a single connection. The idea behind this is to ensure, on an architectural
level, that each beanstalk queue requires as least on Worker to operate, and you won't have one queue filling up because
all your workers are waiting for a job from a different queue.

```php
use Beanie\Beanie;

// get a Worker for a named connection in the pool
$worker = Beanie::pool(['localhost:11300', 'otherhost:11301'])->worker('otherhost:11301');

// tell the Worker to add a tube to the watchlist
$worker->watch('some-tube');

// now let's get ourselves some work to do
$job = $worker->reserve();

// get the data…
echo $job->getData();

// … and delete the job
$job->delete();
```

### Manager

The *Managers* do exactly what it says on the package. They're your go-to class for writing code to get a view on how
your beanstalk instances are performing, or occasionally kicking buried jobs on to the queue again. They expose
statistics on every connection and every tube on every connection.

```php
use Beanie\Beanie;
use Beanie\Tube\Tube;

// get a Manager instance for each connection in the pool
$managers = Beanie::pool(['localhost:11300', 'otherhost:11301'])->managers();

// print stats for each connection in the pool
foreach ($managers as $manager) {
    print_r($manager->stats());
}

// print stats for each tube of the first connection in the pool
$manager = reset($managers);

array_map(
    function (Tube $tube) {
        print_r($tube->stats());
    },
    $manager->tubes();
);
```

## Architecture

### Use case

Each producer writes to random connections on the pool. Each worker handles a single connection -- which doesn't
preclude the possibility of having multiple workers for each queue, of course.

This is a PHP library, and as such, is optimized for the most common use case - short lived producers which create jobs
during page generation and offload them to longer lived workers.

[![HTML View on Gliffy](http://www.gliffy.com/go/publish/image/8600841/L.png)](http://www.gliffy.com/go/publish/8600841)

### Class map

Classes a "casual" user would come into contact with are highlighted in green.

[![HTML View on Gliffy](http://www.gliffy.com/go/publish/image/8559467/L.png)](https://www.gliffy.com/go/publish/8559467)

## License

Copyright (c) 2015 Ilias Van Peer

Released under the MIT License, see the enclosed `LICENSE` file.
