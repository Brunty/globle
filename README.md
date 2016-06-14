# Globle - a PHP Container

[![Build Status](https://travis-ci.org/Brunty/Globle.svg?branch=master)](https://travis-ci.org/Brunty/Globle) [![Coverage Status](https://coveralls.io/repos/github/Brunty/Globle/badge.svg?branch=master)](https://coveralls.io/github/Brunty/Globle?branch=master) [![Latest Stable Version](https://poser.pugx.org/brunty/globle/v/stable)](https://packagist.org/packages/brunty/globle) [![License](https://poser.pugx.org/brunty/globle/license)](https://packagist.org/packages/brunty/globle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/a7e48ce0-7a7f-492a-8da8-d7b4c94f00c8/mini.png)](https://insight.sensiolabs.com/projects/a7e48ce0-7a7f-492a-8da8-d7b4c94f00c8)

## Installation

Through composer:

`composer require brunty/globle`

## Interop

Globle implements `\Interop\Container\ContainerInterface`

## Usage

### Binding

You can bind items into the container in a few different ways:

Via the constructor:

```php
$items = [
    MyClass::class  =>  function() {
        return new MyClass;
    }
];

$globle = new \Brunty\Globle\Globle($items);
```

This will bind to the container where the key of the array is the ID used to retrieve an item from the container.


You can also bind after the container has been instantiated, simply call the `bind` function and pass it an ID and a callable.

```php

$globle = new \Brunty\Globle\Globle;

$globle->bind(MyClass::class, function() {
    return new MyClass;
});
```

**By default, each time you call `get($id)` you will receive the same instance of the class as the first time it was resolved.**

**If you want to get a new instance each time, you can bind it into the container via an additional array of identifiers in the constructor:**
 
```php
$items = [
    MyClass::class  =>  function() {
        return new MyClass;
    }
];

$factories = [MyClass::class];

$globle = new \Brunty\Globle\Globle($items, $factories);
```

**Or by binding via the `factory` method.**

```php

$globle = new \Brunty\Globle\Globle;

$globle->factory(MyClass::class, function() {
    return new MyClass;
});
```

### Getting objects

Calling `$container->get($id);` will retrieve the defined item in the container.


### Using the container to inject dependencies into other items:

The function defined when binding an item into the container can have an instance of `Interop\Container\ContainerInterface` passed to it as a parameter, this will be the container itself so can be used to get other items.

For example:

```php



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

$globle = new \Brunty\Globle\Globle;

$globle->bind(Bar::class, function() {
    return new Bar;
});

$globle->bind(Foo::class, function(\Interop\Container\ContainerInterface $globle) {
    return new Foo($globle->get(Bar::class));
});
```

