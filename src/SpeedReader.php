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

use Webmozart\Assert\Assert;

class SpeedReader
{
    private static $_typeArray       = 'array';
    private static $_typeArrayAccess = 'arrayAccess';
    private static $_typeObject      = 'object';
    private static $_value           = 'value';
    private static $_randomString    = 'UyJCRsMnME7atAu4X5GQptg3HF83PeSZYTjafdzWaJ6wJrzktfkWEvFhf3q5kDLK4k6eq2weqEQDL';

    /**
     * Get the nested value as an integer. The default will be returned if the nested property does not exist, or if
     * the nested value is not scalar.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param int                       $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), if the
     *                                   path is not a string or array of strings, or if the default is needed and is
     *                                   not an integer
     *
     * @return int
     */
    public static function getAsInteger($subject, $path, $default = 0)
    {
        return (int) self::_getPreCastValue($subject, $path, $default, 'is_scalar', __FUNCTION__, 'is_int');
    }

    /**
     * Get the nested value as a float. The default will be returned if the nested property does not exist, or if
     * the nested value is not scalar.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param float                     $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), if the path
     *                                   is not a string or array of strings, or if the default is needed and is not a
     *                                   float
     *
     * @return float
     */
    public static function getAsFloat($subject, $path, $default = 0.0)
    {
        return (float) self::_getPreCastValue($subject, $path, $default, 'is_scalar', __FUNCTION__, 'is_float');
    }

    /**
     * Get the nested value as a boolean. The default will be returned if the nested property does not exist.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param bool                      $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), if the path
     *                                   is not a string or array of strings, or if the default is needed and is not a
     *                                   boolean
     *
     * @return bool
     */
    public static function getAsBoolean($subject, $path, $default = false)
    {
        return (bool) self::_getPreCastValue($subject, $path, $default, '', __FUNCTION__, 'is_bool');
    }

    /**
     * Get the nested value as a string. The default will be returned if the nested property does not exist, or if
     * the nested value is not scalar.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param string                    $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), if the path
     *                                   is not a string or array of strings, or if the default is needed and is not a
     *                                   string
     *
     * @return string
     */
    public static function getAsString($subject, $path, $default = '')
    {
        return (string) self::_getPreCastValue($subject, $path, $default, 'is_scalar', __FUNCTION__, 'is_string');
    }

    /**
     * Get the nested value as an array. The default will be returned if the nested property does not exist, or if
     * the nested value is not an array.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param array                     $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), or if the
     *                                   path is not a string or array of strings
     *
     * @return array
     */
    public static function getAsArray($subject, $path, array $default = array())
    {
        return (array) self::_getPreCastValue($subject, $path, $default, 'is_array', __FUNCTION__);
    }

    /**
     * Get the nested value. The default will be returned if the nested property does not exist.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     * @param mixed                     $default
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object), if the path
     *                                   is not a string or array of strings
     *
     * @return mixed
     */
    public static function get($subject, $path, $default = null)
    {
        return self::_getPreCastValue($subject, $path, $default);
    }

    /**
     * Determine if the subject contains a property specified by the given path.
     *
     * @param array|\ArrayAccess|object $subject
     * @param string|array              $path
     *
     * @throws \InvalidArgumentException if the subject is not a readable type (array, \ArrayAccess, object) or if the
     *                                   path is not a string or array of strings
     *
     * @return bool
     */
    public static function has($subject, $path)
    {
        $search = self::_search($subject, $path);

        return isset($search[self::$_value]);
    }

    /**
     * @param mixed $subject
     *
     * @return bool true if this reader can "read" the given item, false otherwise
     */
    public static function isReadable($subject)
    {
        return is_array($subject) || is_object($subject);
    }

    private static function _getPreCastValue($subject, $path, $default, $valueTest = '', $method = '', $defaultTest = '')
    {
        $search = self::_search($subject, $path);
        $found  = isset($search[self::$_value]);

        if ($found && (!$valueTest || $valueTest($search[self::$_value]))) {

            return $search[self::$_value];
        }

        if ($defaultTest && !$defaultTest($default)) {

            $message = sprintf('Invalid default supplied to %s::%s()', __CLASS__, $method);

            throw new \InvalidArgumentException($message);
        }

        return $default;
    }

    private static function _search($subject, $path)
    {
        Assert::true(self::isReadable($subject), 'Subject must be an array or object');

        if (is_array($path)) {

            Assert::allString($path, 'Path must be a string or an array of strings');

            $pathSegments = $path;

        } elseif (!is_string($path)) {

            throw new \InvalidArgumentException('Path must be a string or an array of strings');

        } else {

            $pathSegments = self::_pathStringToArray($path);
        }

        $node = $subject;

        foreach ($pathSegments as $pathSegment) {

            $nodeType = self::_getType($node);

            if (self::_reachedEndOfSearch($node, $nodeType, $pathSegment)) {

                return array();
            }

            $node = self::_getNextNode($node, $nodeType, $pathSegment);
        }

        return array(self::$_value => $node);
    }

    private static function _pathStringToArray($raw)
    {
        $rando  = self::$_randomString . time();
        $modded = str_replace('\.', $rando, $raw);
        $parts  = explode('.', $modded);

        return array_map(function ($part) use ($rando) {

            return str_replace($rando, '.', $part);

        }, $parts);
    }

    private static function _getNextNode($intermediate, $type, $key)
    {
        switch ($type) {

            case self::$_typeArray:
            case self::$_typeArrayAccess:

                return $intermediate[$key];

            case self::$_typeObject:

                return $intermediate->$key;

            default:

                return null;
        }
    }

    private static function _reachedEndOfSearch($intermediate, $type, $key)
    {
        switch ($type) {

            case self::$_typeArray:

                return !array_key_exists($key, $intermediate);

            case self::$_typeArrayAccess:

                /** @var \ArrayAccess $aa */
                $aa = $intermediate;

                return !$aa->offsetExists($key);

            case self::$_typeObject:

                return !property_exists($intermediate, $key);

            default:

                return true;
        }
    }

    private static function _getType($candidate)
    {
        if (is_array($candidate)) {

            return self::$_typeArray;
        }

        if ($candidate instanceof \ArrayAccess) {

            return self::$_typeArrayAccess;
        }

        if (is_object($candidate)) {

            return self::$_typeObject;
        }

        return null;
    }
}
