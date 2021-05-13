<?php

/**
 * @Author: stephan <m6ahenina@gmail.com>
 * @Date:   2019-08-19 22:33:21
 * @Last Modified by:   stephan <m6ahenina@gmail.com>
 * @Last Modified time: 2020-02-26 13:58:49
 */

namespace App\Utils;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Static Resolver
 */
class Resolver
{
    public static function resolve(array $paths = [], $default = "")
    {
        if (empty($paths)) {
            return $default;
        }
        $accessor = PropertyAccess::createPropertyAccessor();
        $tmp = array_shift($paths);
        if (!$tmp) {
            return $default;
        }

        foreach ($paths as $key) {
            if (is_null($key)) {
                return $default;
            }
            if (is_array($tmp)) {
                $key = '[' . $key . ']';
            }
            $tmp = $accessor->getValue($tmp, $key);
            if (is_null($tmp)) {
                return $default;
            }
        }

        return $tmp;
    }

    public static function setValue(array $paths = [], $prop, $value)
    {
        $obj = self::resolve($paths, null);
        if ($obj) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $accessor->setValue($obj, $prop, $value);
        }
    }
}
