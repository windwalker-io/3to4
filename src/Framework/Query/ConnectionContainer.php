<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query;

/**
 * The Connection Container class.
 *
 * @since  2.0
 */
abstract class ConnectionContainer
{
    /**
     * Property connections.
     *
     * @var  \PDO[]|resource[]
     */
    protected static $connections = [];

    /**
     * getConnection
     *
     * @param string $driver
     *
     * @return  null|\PDO|resource
     */
    public static function getConnection($driver)
    {
        $driver = strtolower($driver);

        if (empty(static::$connections[$driver])) {
            return null;
        }

        return static::$connections[$driver];
    }

    /**
     * setConnection
     *
     * @param string        $driver
     * @param \PDO|resource $connection
     *
     * @return  void
     */
    public static function setConnection($driver, $connection)
    {
        $driver = strtolower($driver);

        static::$connections[$driver] = $connection;
    }
}
