<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Cubrid;

use Windwalker\Legacy\Query\Query;

/**
 * Class CubridQuery
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class CubridQuery extends Query
{
    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = 'cubrid';

    /**
     * The character(s) used to quote SQL statement names such as table names or field names,
     * etc. The child classes should define this as necessary.  If a single character string the
     * same character is used for both sides of the quoted name, else the first character will be
     * used for the opening quote and the second for the closing quote.
     *
     * @var    string
     * @since  2.0
     */
    protected $nameQuote = '`';

    /**
     * The null or zero representation of a timestamp for the database driver.  This should be
     * defined in child classes to hold the appropriate value for the engine.
     *
     * @var    string
     * @since  2.0
     */
    protected $nullDate = '0000-00-00 00:00:00';
}
