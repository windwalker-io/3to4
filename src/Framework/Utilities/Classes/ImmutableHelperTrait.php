<?php declare(strict_types=1);
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    Please see LICENSE file.
 */

namespace Windwalker\Legacy\Utilities\Classes;

/**
 * The ImmutableHelperTrait class.
 *
 * @since  3.2
 */
trait ImmutableHelperTrait
{
    /**
     * getReturnInstance
     *
     * @param callable $callback
     *
     * @return static
     */
    protected function cloneInstance(callable $callback = null)
    {
        $new = clone $this;

        if ($callback === null) {
            return $new;
        }

        $callback($new);

        return $new;
    }
}
