# Beanie
> A clean, lean PHP beanstalkd client library

[![Build Status](https://travis-ci.org/zwilias/beanie.svg?branch=master)](https://travis-ci.org/zwilias/beanie)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zwilias/beanie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zwilias/beanie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/zwilias/beanie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/zwilias/beanie/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/zwilias/beanie/v/stable)](https://packagist.org/packages/zwilias/beanie) 
[![Total Downloads](https://poser.pugx.org/zwilias/beanie/downloads)](https://packagist.org/packages/zwilias/beanie) 
[![Latest Unstable Version](https://poser.pugx.org/zwilias/beanie/v/unstable)](https://packagist.org/packages/zwilias/beanie) 
[![License](https://poser.pugx.org/zwilias/beanie/license)](https://packagist.org/packages/zwilias/beanie)

## Core features

- Support for connection pools
- Clean distinction between concerns: Producers, Workers and Managers
- Clean interface. Want to touch a Job? `$job->touch();`
- Full support for the beanstalk protocol [as documented][protocol] at the time of writing

[protocol]: https://github.com/kr/beanstalkd/blob/master/doc/protocol.md

## Quickstart

Requirements:

- PHP 5.5, 5.6 or 7.x
- beanstalkd 1.3 or higher
- PHP `socket_*` functions must not be disabled

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

## Installation

Installation is recommended to happen through [composer](https://getcomposer.org/).

```
# Install composer
$ curl -sS https://getcomposer.org/installer | php

# Require Beanie
$ php composer.phar require zwilias/beanie

# Check your vendor/ directory!
```

## Architecture

### Use case

Each producer writes to random connections on the pool. Each worker handles a single connection -- which doesn't
preclude the possibility of having multiple workers for each queue, of course.

This is a PHP library, and as such, is optimized for the most common use case - short lived producers which create jobs
during page generation and offload them to longer lived workers.

[![HTML View on Gliffy](http://www.gliffy.com/go/publish/image/8600841/L.png)](http://www.gliffy.com/go/publish/8600841)

**However**: using the `Worker::reserveOath` method which returns a `JobOath` object, one could poll multiple workers
for a Job. The reserveOath method writes the blocking reserve command to the beanstalk connection, but does not enter
the blocking read call until the `invoke` method is called on the returned `JobOath` object. The `JobOath` object also
exposes the raw socket resource, so using `socket_select` or something like and `\EvIo` watcher could enable a use-case
like so:

[![HTML View on Gliffy](http://www.gliffy.com/go/publish/image/8630261/L.png)](http://www.gliffy.com/go/publish/8630261)

A *Queue Manager*  PHP library named *QMan* which provides such functionality is currently in the works.

### Class map

Classes a "casual" user would come into contact with are highlighted in green.

[![HTML View on Gliffy](http://www.gliffy.com/go/publish/image/8559467/L.png)](https://www.gliffy.com/go/publish/8559467)

## Contributing

Pull requests are appreciated. Make sure code-quality (according to [scrutinizer](https://scrutinizer-ci.com/)) doesn't 
suffer too badly. Make sure you add thorough white-box unit tests and, if applicable, black-box integration tests.

Running the tests locally:

```
$ git clone https://github.com/zwilias/beanie.git
$ cd Beanie
$ composer install
$ vendor/bin/phpunit
```

**Note**: Some of the integration tests depend on a locally running beanstalkd. These tests are excluded in the default
phpunit.xml.dist file. In order to include them, run phpunit with the `--group __nogroup__,beanstalk` flag. If you want
the tests to connect to a server other than `localhost:11300`, set the `BEANSTALK_HOST` and `BEANSTALK_PORT` environment
variables.

## License

Copyright (c) 2015 Ilias Van Peer

Released under the MIT License, see the enclosed `LICENSE` file.
