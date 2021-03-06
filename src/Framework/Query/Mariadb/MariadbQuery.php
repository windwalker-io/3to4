<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Mariadb;

use Windwalker\Legacy\Query\Mysql\MysqlQuery;

/**
 * Class Mariadb
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class MariadbQuery extends MysqlQuery
{
    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = 'mariadb';
}
