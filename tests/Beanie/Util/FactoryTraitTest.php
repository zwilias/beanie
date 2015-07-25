<?php


namespace Beanie\Util;


class FactoryTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var FactoryTrait|\PHPUnit_Framework_MockObject_MockObject */
    private $factory;

    public function setUp()
    {
        $this->factory = $this
            ->getMockBuilder(FactoryTrait::class)
            ->getMockForTrait();
    }

    public function testInstance_createsInstance()
    {
        $factory = $this->factory->instance();


        $this->assertNotNull($factory);
    }

    public function testInstance_consecutiveCalls_onlyOneInstance()
    {
        $firstFactory = $this->factory->instance();
        $otherFactory = $this->factory->instance();

        $this->assertSame($firstFactory, $otherFactory);
    }

    public function testUnsetInstance_unsetsInstance()
    {
        $firstFactory = $this->factory->instance();
        $this->factory->unsetInstance();
        $otherFactory = $this->factory->instance();

        $this->assertNotSame($firstFactory, $otherFactory);
    }

    public function tearDown()
    {
        $this->factory->unsetInstance();
    }
}
