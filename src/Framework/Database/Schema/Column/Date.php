<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Database\Schema\Column;

use Windwalker\Legacy\Database\Schema\Column;
use Windwalker\Legacy\Database\Schema\DataType;

/**
 * The DATE class.
 *
 * @since  3.2.8
 * @deprecated Legacy code
 */
class Date extends Column
{
    /**
     * Class init.
     *
     * @param string $name
     * @param bool   $allowNull
     * @param string $default
     * @param string $comment
     * @param array  $options
     */
    public function __construct($name = null, $allowNull = false, $default = null, $comment = '', $options = [])
    {
        parent::__construct($name, DataType::DATE, true, $allowNull, $default, $comment, $options);
    }
}
