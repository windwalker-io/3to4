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
 * The Varchar class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class Integer extends Column
{
    /**
     * Class init.
     *
     * @param string $name
     * @param int    $length
     * @param bool   $signed
     * @param bool   $allowNull
     * @param string $default
     * @param string $comment
     * @param array  $options
     */
    public function __construct(
        $name = null,
        $length = null,
        $signed = false,
        $allowNull = false,
        $default = 0,
        $comment = '',
        $options = []
    ) {
        $options['length'] = $length;

        parent::__construct($name, DataType::INTEGER, $signed, $allowNull, $default, $comment, $options);
    }
}
