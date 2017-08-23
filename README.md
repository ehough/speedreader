# SpeedReader [![Build Status](https://secure.travis-ci.org/ehough/speedreader.png)](http://travis-ci.org/ehough/speedreader)

Nested-property selector for PHP 5.3+ with a focus on performance.

SpeedReader allows you to use dot notation (a subset of [JMESPath expressions and subexpressions](http://jmespath.org/tutorial.html#basic-expressions))
to quickly read values from arrays, [\ArrayAccess](https://secure.php.net/manual/class.arrayaccess.php) instances, objects, or any combination thereof. You can provide a
fallback value to use if the property does not exist or cannot be casted to the desired type.

## Installation

Install the latest version with

```bash
$ composer require ehough/speedreader
```

## Basic Usage

SpeedReader can read from any mix of arrays, \ArrayAccess instances, and objects.

```php
<?php

use Hough\SpeedReader\SpeedReader;

$arrayAccess = new \ArrayIterator(array(
    'hello' => 'there',
));

$object = new stdClass();
$object->foo = 99;

$subject = array(
    'foo' => array(
        'bar' => array(
            'int'         => 123,
            'float'       => 456.7,
            'bool'        => true,
            'object'      => $object,
            'array'       => array('foo', 'bar'),
            'string'      => 'hello!',
            'arrayAccess' => $arrayAccess,
        ),
    ),
);
```

### `SpeedReader::getAsInteger()`

Gets the value, casted to integer, at the given path. If no fallback is supplied, `0` will be returned.

```php
SpeedReader::getAsInteger($subject, 'foo.bar.float');               /* int(456)  */
SpeedReader::getAsInteger($subject, 'foo.bar.float', 99);           /* int(456)  */
SpeedReader::getAsInteger($subject, 'not.found', 99);               /* int(99)   */
SpeedReader::getAsInteger($subject, 'not.found');                   /* int(99)   */
SpeedReader::getAsInteger($subject, 'foo.bar.object.foo');          /* int(99)   */
SpeedReader::getAsInteger($subject, 'foo.bar.int');                 /* int(123)  */
SpeedReader::getAsInteger($subject, 'foo.bar.bool');                /* int(1)    */
SpeedReader::getAsInteger($subject, 'foo.bar.object', 99);          /* int(99)   */
SpeedReader::getAsInteger($subject, 'foo.bar.array', 99);           /* int(99)   */
SpeedReader::getAsInteger($subject, 'foo.bar.string');              /* int(0)    */
SpeedReader::getAsInteger($subject, 'foo.bar.arrayAccess', 99);     /* int(99)   */
SpeedReader::getAsInteger($subject, 'foo.bar.arrayAccess.hello');   /* int(0)    */
```

### `SpeedReader::getAsString()`

Gets the value, casted to string, at the given path. If no fallback is supplied, an empty string will be returned.

```php
SpeedReader::getAsString($subject, 'foo.bar.float');                   /* string(5) "456.7"     */
SpeedReader::getAsString($subject, 'foo.bar.float', 'fallback');       /* string(5) "456.7"     */
SpeedReader::getAsString($subject, 'not.found', 'fallback');           /* string(8) "fallback"  */
SpeedReader::getAsString($subject, 'not.found');                       /* string(0) ""          */
SpeedReader::getAsString($subject, 'foo.bar.object.foo');              /* string(2) "99"        */
SpeedReader::getAsString($subject, 'foo.bar.int');                     /* string(3) "123"       */
SpeedReader::getAsString($subject, 'foo.bar.bool');                    /* string(1) "1"         */
SpeedReader::getAsString($subject, 'foo.bar.object', 'fallback');      /* string(8) "fallback"  */
SpeedReader::getAsString($subject, 'foo.bar.array', 'fallback');       /* string(8) "fallback"  */
SpeedReader::getAsString($subject, 'foo.bar.string');                  /* string(6) "hello!"    */
SpeedReader::getAsString($subject, 'foo.bar.arrayAccess', 'fallback'); /* string(8) "fallback"  */
SpeedReader::getAsString($subject, 'foo.bar.arrayAccess.hello');       /* string(5) "there"     */
```

### `SpeedReader::getAsFloat()`

Gets the value, casted to float, at the given path. If no fallback is supplied, `0.0` will be returned.

```php
SpeedReader::getAsFloat($subject, 'foo.bar.float');               /* double(456.7)  */
SpeedReader::getAsFloat($subject, 'foo.bar.float', 99.9);         /* double(456.7)  */
SpeedReader::getAsFloat($subject, 'not.found', 99.9);             /* double(99.9)   */
SpeedReader::getAsFloat($subject, 'not.found');                   /* double(0)      */
SpeedReader::getAsFloat($subject, 'foo.bar.object.foo');          /* double(99)     */
SpeedReader::getAsFloat($subject, 'foo.bar.int');                 /* double(123)    */
SpeedReader::getAsFloat($subject, 'foo.bar.bool');                /* double(1)      */
SpeedReader::getAsFloat($subject, 'foo.bar.object', 99.9);        /* double(99.9)   */
SpeedReader::getAsFloat($subject, 'foo.bar.array', 99.9);         /* double(99.9)   */
SpeedReader::getAsFloat($subject, 'foo.bar.string');              /* double(0)      */
SpeedReader::getAsFloat($subject, 'foo.bar.arrayAccess', 99.9);   /* double(99.9)   */
SpeedReader::getAsFloat($subject, 'foo.bar.arrayAccess.hello');   /* double(0)      */
```

### `SpeedReader::getAsBoolean()`

Gets the value, casted to boolean, at the given path. If no fallback is supplied, `false` will be returned.

```php
SpeedReader::getAsBoolean($subject, 'foo.bar.float');               /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.float', true);         /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'not.found', true);             /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'not.found');                   /* bool(false) */
SpeedReader::getAsBoolean($subject, 'foo.bar.object.foo');          /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.int');                 /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.bool');                /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.object', true);        /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.array', true);         /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.string');              /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.arrayAccess', true);   /* bool(true)  */
SpeedReader::getAsBoolean($subject, 'foo.bar.arrayAccess.hello');   /* bool(true)  */
```

### `SpeedReader::getAsArray()`

Gets the value, casted to array, at the given path. If no fallback is supplied, an empty array will be returned.

```php
SpeedReader::getAsArray($subject, 'foo.bar.float');                        /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.float', array('foobar'));       /* array(1) { [0] => string(6) "foobar" }  */
SpeedReader::getAsArray($subject, 'not.found', array('foobar'));           /* array(1) { [0] => string(6) "foobar" }  */
SpeedReader::getAsArray($subject, 'not.found');                            /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.object.foo');                   /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.int');                          /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.bool');                         /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.object', array('foobar'));      /* array(1) { [0] => string(6) "foobar" }  */
SpeedReader::getAsArray($subject, 'foo.bar.array', array('foobar'));       /* array(2) { [0] => string(3) "foo" [1] => string(3) "bar" }
SpeedReader::getAsArray($subject, 'foo.bar.string');                       /* array(0) {}  */
SpeedReader::getAsArray($subject, 'foo.bar.arrayAccess', array('foobar')); /* array(1) { [0] => string(6) "foobar" }  */
SpeedReader::getAsArray($subject, 'foo.bar.arrayAccess.hello');            /* array(0) {}  */
```

### `SpeedReader::get()`

Gets the value at the given path. If no fallback is supplied, `null` will be returned.

```php
SpeedReader::get($subject, 'foo.bar.float');                    /* double(456.7)  */
SpeedReader::get($subject, 'foo.bar.float', 567.8);             /* double(456.7) */
SpeedReader::get($subject, 'not.found', 'foobar');              /* string(6) "foobar" */
SpeedReader::get($subject, 'not.found');                        /* NULL  */
SpeedReader::get($subject, 'foo.bar.object.foo');               /* int(99)  */
SpeedReader::get($subject, 'foo.bar.int');                      /* int(123)  */
SpeedReader::get($subject, 'foo.bar.bool');                     /* array(1) { [0] => string(6) "foobar" } */
SpeedReader::get($subject, 'foo.bar.object', null);             /* class stdClass#2 (1) { public $foo => int(99) }  */
SpeedReader::get($subject, 'foo.bar.array', array('foobar'));   /* array(2) { [0] => string(3) "foo" [1] => string(3) "bar" } */
SpeedReader::get($subject, 'foo.bar.string');                   /* string(6) "hello!"  */
SpeedReader::get($subject, 'foo.bar.arrayAccess', null);        /* class ArrayIterator#3 (1) { ...   */
SpeedReader::get($subject, 'foo.bar.arrayAccess.hello');        /* string(5) "there"  */
```

### `SpeedReader::has()`

Determines if the subject contains a value (including `null`) at the given path.

```php
SpeedReader::has($subject, 'foo.bar.float');              /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.float');              /* bool(true)  */
SpeedReader::has($subject, 'not.found');                  /* bool(false) */
SpeedReader::has($subject, 'foo.bar.object.foo');         /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.int');                /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.bool');               /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.object');             /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.array');              /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.string');             /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.arrayAccess');        /* bool(true)  */
SpeedReader::has($subject, 'foo.bar.arrayAccess.hello');  /* bool(true)  */
```

## About

### Requirements

SpeedReader works with PHP 5.3 and above.

### License

SpeedReader is licensed under the Mozilla Public License 2.0. See the `LICENSE` file for details.