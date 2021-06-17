<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Filter;

/**
 * The NullFilter class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class NullFilter
{
    /**
     * clean
     *
     * @param string                 $source
     * @param string|callable|object $filter
     *
     * @return  mixed
     */
    public function clean($source, $filter = 'string')
    {
        return $source;
    }
}
