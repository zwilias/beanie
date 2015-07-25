<?php


namespace Beanie\Tube;


class ValidNameCheckerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ValidNameChecker */
    protected $validNameChecker;

    public function setUp()
    {
        $this->validNameChecker = new ValidNameChecker();
    }

    /**
     * @param $validName
     * @dataProvider validNamesProvider
     */
    public function testEnsureValidName_validName($validName)
    {
        $this->assertTrue($this->validNameChecker->ensureValidName($validName));
    }

    /**
     * @param $invalidName
     * @expectedException \Beanie\Exception\InvalidNameException
     * @expectedExceptionCode 400
     * @dataProvider invalidNamesProvider
     */
    public function testEnsureValidName_invalidName_throwsException($invalidName)
    {
        $this->validNameChecker->ensureValidName($invalidName);
    }

    public function validNamesProvider()
    {
        return [
            ['default'],
            ['(why-_such'],
            ['_N4me)s$'],
            ['A-Za-z0-9+/;.$_()-'],
            [str_repeat('a', 200)]
        ];
    }

    public function invalidNamesProvider()
    {
        return [
            [true],
            [10],
            [''],
            [new \stdClass()],
            ['contains spaces'],
            ['-startsWithHyphen'],
            ['contains@illegalCharacter'],
            [str_repeat('a', 201)]
        ];
    }
}
