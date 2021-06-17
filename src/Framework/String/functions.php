<?php declare(strict_types=1);
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    Please see LICENSE file.
 */

use Windwalker\Legacy\String\StringObject;

if (!function_exists('str')) {
    /**
     * str
     *
     * @param string      $string
     * @param null|string $encoding
     *
     * @return  StringObject
     *
     * @deprecated Legacy code.
     */
    function str($string = '', $encoding = StringObject::ENCODING_UTF8)
    {
        return new StringObject($string, $encoding);
    }
}
