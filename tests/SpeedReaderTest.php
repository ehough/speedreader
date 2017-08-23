<?php
/*
 * Copyright 2017 Eric D. Hough (https://github.com/ehough)
 *
 * This file is part of SpeedReader.
 *
 *   https://github.com/ehough/speedreader
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace Hough\SpeedReader;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Hough\SpeedReader\SpeedReader
 */
class SpeedReaderTest extends TestCase
{
    /**
     * @dataProvider getDataBadPaths
     */
    public function testBadPaths($path)
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Path must be a string or an array of strings');

        SpeedReader::getAsString(array(), $path, 'default');
    }

    public function getDataBadPaths()
    {
        return array(
            array(new \stdClass()),
            array(33),
            array(33.0),
            array(true),
        );
    }

    /**
     * @dataProvider dataHas
     */
    public function testHas($tree, $path, $expected)
    {
        $this->assertEquals($expected, SpeedReader::has($tree, $path));
    }

    /**
     * @dataProvider dataGetNoDefault
     */
    public function testGetNoDefault($tree, $path, $expected)
    {
        $this->assertEquals($expected, SpeedReader::get($tree, $path));
    }

    /**
     * @dataProvider dataGetWithDefault
     */
    public function testGetWithDefault($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::get($tree, $path, $default));
    }

    /**
     * @dataProvider dataGetAsFloat
     */
    public function testGetAsFloat($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::getAsFloat($tree, $path, $default));
    }

    public function dataGetAsFloat()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'float', 66.0, 123.4),
            array($tree, 'no.exist', 66.0, 66.0),
            array($tree, 'int', 66.0, 99.0),
            array($tree, 'bool', 66.0, 1.0),
            array($tree, 'array', 66.0, 66.0),
            array($tree, 'obj', 66.0, 66.0),
        );
    }

    /**
     * @dataProvider dataGetAsString
     */
    public function testGetAsString($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::getAsString($tree, $path, $default));
    }

    public function dataGetAsString()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'float', 'hello', '123.4'),
            array($tree, 'no.exist', 'hello', 'hello'),
            array($tree, 'int', 'hello', '99'),
            array($tree, 'bool', 'hello', '1'),
            array($tree, 'array', 'hello', 'hello'),
            array($tree, 'obj', 'hello', 'hello'),
        );
    }

    /**
     * @dataProvider dataGetAsArray
     */
    public function testGetAsArray($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::getAsArray($tree, $path, $default));
    }

    public function dataGetAsArray()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'float', array('hi'), array('hi')),
            array($tree, 'no.exist', array('hi'), array('hi')),
            array($tree, 'int', array('hi'), array('hi')),
            array($tree, 'bool', array('hi'), array('hi')),
            array($tree, 'array', array('hi'), array('foo', 'bar')),
            array($tree, 'obj', array('hi'), array('hi')),
        );
    }

    /**
     * @dataProvider dataGetAsBoolean
     */
    public function testGetAsBoolean($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::getAsBoolean($tree, $path, $default));
    }

    public function dataGetAsBoolean()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'float', false, true),
            array($tree, 'no.exist', true, true),
            array($tree, 'int', false, true),
            array($tree, 'bool', false, true),
            array($tree, 'array', false, true),
            array($tree, 'obj', false, true),
        );
    }

    /**
     * @dataProvider dataGetAsInteger
     */
    public function testGetAsInteger($tree, $path, $default, $expected)
    {
        $this->assertEquals($expected, SpeedReader::getAsInteger($tree, $path, $default));
    }

    public function dataGetAsInteger()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'float', 66, 123),
            array($tree, 'no.exist', 66, 66),
            array($tree, 'int', 66, 99),
            array($tree, 'bool', 66, 1),
            array($tree, 'array', 66, 66),
            array($tree, 'obj', 66, 66),
        );
    }

    public function dataHas()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'foo', true),
            array($tree, 'foo.bar', true),
            array($tree, 'foo.bar.deeper', true),
            array($tree, 'foo.bar.deeper.dog', true),
            array($tree, 'foo.bar.deeper.dog.bla', true),
            array($tree, array('foo', 'bar', 'deeper', 'dog', 'bla'), true),
            array($tree, 'foo.bar.baz', false),
            array($tree, 'bar', true),
            array($tree, 'bar.hello', true),
            array($tree, 'bar.hello.yo', true),
            array($tree, 'bar.hello.yo.good', true),
            array($tree, 'bar.hello.yo.good.not', false),
        );
    }

    public function dataGetWithDefault()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'foo.bar.deeper.dog.bla', 33, 'bla'),
            array($tree, 'bar.hello.yo.good', 33, 'stuff'),
            array($tree, 'no.existay', 33, 33),
        );
    }

    public function dataGetNoDefault()
    {
        $tree = $this->_dataTree();

        return array(
            array($tree, 'foo.bar.deeper.dog.bla', 'bla'),
            array($tree, 'bar.hello.yo.good', 'stuff'),
        );
    }

    private function _dataTree()
    {
        $fooObject   = new \stdClass();

        $fooObject->bar = array(
            'hi'     => 'there',
            'deeper' => array(
                'dog' => new \ArrayIterator(array(
                    'bla' => 'bla',
                )),
            ),
        );

        $yoObject       = new \stdClass();
        $yoObject->good = 'stuff';

        $arrayAccess = new \ArrayIterator(array(
            'hello' => array(
                'yo' => $yoObject,
            ),
        ));

        return array(
            'foo'   => $fooObject,
            'bar'   => $arrayAccess,
            'float' => 123.4,
            'int'   => 99,
            'obj'   => new \stdClass(),
            'array' => array('foo', 'bar'),
            'bool'  => true,
        );
    }
}
