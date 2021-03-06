<?php declare(strict_types=1);
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    Please see LICENSE file.
 */

namespace Windwalker\Legacy\Utilities\Classes;

/**
 * Interface StringableInterface
 *
 * @since  3.2
 */
interface StringableInterface
{
    /**
     * Magic method to convert this object to string.
     *
     * @return  string
     */
    public function __toString();
}
