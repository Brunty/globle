<?php

namespace Brunty\Globle\Tests;

use Brunty\Globle\Globle;

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
     * @expectedException \InvalidArgumentException
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
}