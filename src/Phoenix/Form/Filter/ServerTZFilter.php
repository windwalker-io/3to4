<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2017 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Phoenix\Form\Filter;

use Windwalker\Legacy\Core\DateTime\Chronos;

/**
 * The ServerTZFilter class.
 *
 * @since  1.8.13
 * @deprecated Legacy code
 */
class ServerTZFilter extends TimezoneFilter
{
    /**
     * TimezoneFilter constructor.
     *
     * @param string $from
     */
    public function __construct($from = null)
    {
        $to = Chronos::getServerDefaultTimezone();

        parent::__construct($from, $to);
    }
}
