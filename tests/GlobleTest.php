<?php

namespace Brunty\Globle\Tests;

use Brunty\Globle\Globle;
use Interop\Container\ContainerInterface;

class GlobleTest extends \PHPUnit_Framework_TestCase
{

    public function testItSetsTheKeysForItemsCorrectlyWhenConstructed()
    {
        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ]
        );

        $this->assertTrue($sut->has('MyClass'));
    }

    public function testItWillReturnFalseForItemsItDoesNotHave()
    {
        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ]
        );

        $this->assertFalse($sut->has('FooBar'));
    }

    /**
     * @expectedException \Brunty\Globle\Exceptions\NotFoundException
     */
    public function testItThrowsAnExceptionIfRequestingAnObjectThatDoesNotExist()
    {
        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ]
        );

        $sut->get('FooBar');
    }


    public function testItGetsAFreshClassThatHasNotAlreadyBeenResolved()
    {
        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ]
        );

        $object = $sut->get('MyClass');

        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testItGetsTheSameInstanceOfAClassByDefault()
    {

        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ]
        );

        $object = $sut->get('MyClass');
        $secondObject = $sut->get('MyClass');

        $this->assertSame($object, $secondObject);
    }

    public function testItGetsANewClassEveryTimeIfItsBoundAsAFactoryThroughTheConstructor()
    {
        $sut = new Globle(
            [
                'MyClass' => function () {
                    return new \stdClass;
                }
            ],
            [
                'MyClass'
            ]
        );

        $object = $sut->get('MyClass');
        $secondObject = $sut->get('MyClass');

        $this->assertNotSame($object, $secondObject);
    }

    public function testItGetsANewClassEveryTimeIfItsBoundThroughTheFactoryMethod()
    {
        $sut = new Globle;
        $sut->factory(
            'MyClass',
            function () {
                return new \stdClass;
            }
        );

        $object = $sut->get('MyClass');
        $secondObject = $sut->get('MyClass');

        $this->assertNotSame($object, $secondObject);
    }

    public function testItGetsTheSameClassEveryTimeIfItsBoundThroughTheBindMethod()
    {
        $sut = new Globle;
        $sut->bind(
            'MyClass',
            function () {
                return new \stdClass;
            }
        );

        $object = $sut->get('MyClass');
        $secondObject = $sut->get('MyClass');

        $this->assertSame($object, $secondObject);
    }

    public function testItCanUseItselfInTheCallableToInjectOtherObjectsItHasBound()
    {

        $sut = new Globle;
        $sut->bind(
            Bar::class,
            function () {
                return new Bar;
            }
        );
        $sut->bind(
            Foo::class,
            function (ContainerInterface $container) {
                return new Foo($container->get(Bar::class));
            }
        );

        $foo = $sut->get(Foo::class);

        $this->assertInstanceOf(Foo::class, $foo);
        $this->assertInstanceOf(Bar::class, $foo->getBar());
    }
}


class Foo
{

    /**
     * @var Bar
     */
    private $bar;

    /**
     * @param Bar $bar
     */
    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    /**
     * @return Bar
     */
    public function getBar()
    {
        return $this->bar;
    }
}

class Bar
{

}
