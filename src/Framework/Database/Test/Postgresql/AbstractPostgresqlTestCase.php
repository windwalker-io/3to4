<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Legacy\Database\Test\Postgresql;

use Windwalker\Legacy\Database\Driver\Postgresql\PostgresqlDriver;
use Windwalker\Legacy\Database\Test\AbstractDatabaseTestCase;

/**
 * The AbstractPostgresqlTest class.
 *
 * @since  2.1
 */
abstract class AbstractPostgresqlTestCase extends AbstractDatabaseTestCase
{
    /**
     * Property driver.
     *
     * @var  string
     */
    protected static $driver = 'postgresql';

    /**
     * Property quote.
     *
     * @var  array
     */
    protected static $quote = ['"', '"'];

    /**
     * Property db.
     *
     * @var PostgresqlDriver
     */
    protected $db;

    /**
     * Property connection.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = static::$dbo;
        $this->connection = $this->db->getConnection();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
