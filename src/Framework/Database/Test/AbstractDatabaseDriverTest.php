<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Database\Test;

use Windwalker\Legacy\Database\Middleware\DbProfilerMiddleware;
use Windwalker\Legacy\Database\Test\Mysql\AbstractMysqlTestCase;
use Windwalker\Legacy\Middleware\MiddlewareInterface;
use Windwalker\Legacy\Query\Query;

/**
 * Test class of AbstractDatabaseDriver
 *
 * @since 3.0
 * @deprecated Legacy code
 */
class AbstractDatabaseDriverTest extends AbstractMysqlTestCase
{
    /**
     * Method to test disconnect().
     *
     * @return void
     */
    public function testMonitor()
    {
        self::markTestIncomplete('This test has not been implemented yet.');
    }
}
