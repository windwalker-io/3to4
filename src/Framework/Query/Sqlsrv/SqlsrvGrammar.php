<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 $Asikart.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Sqlsrv;

use Windwalker\Legacy\Query\AbstractQueryGrammar;
use Windwalker\Legacy\Query\QueryElement;

/**
 * The SqlsrvGrammar class.
 *
 * @since  3.5
 * @deprecated Legacy code
 */
class SqlsrvGrammar extends AbstractQueryGrammar
{
    /**
     * showDatabases
     *
     * @param null $where
     *
     * @return  string
     */
    public static function listDatabases($where = null)
    {
        $where = $where ? new QueryElement('WHERE', $where, 'AND') : null;

        return 'SELECT name FROM master.dbo.sysdatabases ' . $where;
    }

    /**
     * createDatabase
     *
     * @param string $name
     * @param string $collate
     *
     * @return  string
     */
    public static function createDatabase($name, $collate = null)
    {
        $query = static::getQuery();

        return static::build(
            'CREATE DATABASE',
            $query->quoteName($name),
            $collate ? 'COLLATE ' . $collate : null
        );
    }

    /**
     * dropTable
     *
     * @param string $name
     * @param bool   $ifExists
     *
     * @return  string
     */
    public static function dropDatabase($name, $ifExists = null)
    {
        $query = static::getQuery();

        return static::build(
            'DROP DATABASE',
            $ifExists ? 'IF EXISTS' : null,
            $query->quoteName($name)
        );
    }

    /**
     * showTableColumn
     *
     * @param string $table
     *
     * @return  string
     */
    public static function showTableColumns($table)
    {
        // No use
    }

    /**
     * showDbTables
     *
     * @param string $dbname
     *
     * @return  string
     */
    public static function showDbTables($dbname)
    {
        $query = self::getQuery(true);

        return $query->format('SELECT name AS Name FROM %n.sys.Tables WHERE type = %q', $dbname, 'U');
    }

    /**
     * createTable
     *
     * @param string $name
     * @param array  $columns
     *
     * @param array  $pks
     * @param array  $keys
     * @param bool   $ifNotExists
     *
     * @return  string
     */
    public static function createTable(
        $name,
        $columns,
        $pks = [],
        $keys = [],
        $ifNotExists = true
    ) {
        $query = static::getQuery();
        $cols = [];

        foreach ($columns as $cName => $details) {
            $details = (array) $details;

            array_unshift($details, $query->quoteName($cName));

            $cols[] = call_user_func_array([static::class, 'build'], $details);
        }

        if (!is_array($keys)) {
            throw new \InvalidArgumentException('Keys should be an array');
        }

        if ($pks) {
            $cols[] = 'CONSTRAINT ' . $query->quoteName($name . '_pk')
                . ' PRIMARY KEY ' . static::buildIndexDeclare(null, (array) $pks, null);
        }

        $indexes = [];

        foreach ($keys as $key) {
            $define = [
                'type' => 'INDEX',
                'name' => null,
                'columns' => [],
                'comment' => '',
            ];

            if (!is_array($key)) {
                throw new \InvalidArgumentException('Every key data should be an array with "type", "name", "columns"');
            }

            $define = array_merge($define, $key);

            $indexes[] = 'CREATE ' . $define['type'] . ' ' . static::buildIndexDeclare(
                $define['name'],
                $define['columns'],
                $name
            );
        }

        $indexes = implode(";\n", $indexes) . ';';

        $cols = "(\n" . implode(",\n", $cols) . "\n)";

        $sql = static::build(
            'CREATE TABLE',
            $query->quoteName($name),
            $cols,
            ";\n",
            $indexes ? $indexes : null
        );

        if ($ifNotExists) {
            $sql = self::getQuery(true)->format(
                "if not exists (select * from sysobjects where name=%q and xtype='U')\n$sql\ngo;",
                $name
            );
        }

        static::build(
            $sql,
            $indexes ? $indexes : null
        );

        return $sql;
    }

    /**
     * dropTable
     *
     * @param string $table
     * @param bool   $ifExists
     * @param string $option
     *
     * @return  string
     */
    public static function dropTable($table, $ifExists = false, $option = '')
    {
        $query = static::getQuery();

        return static::build(
            'DROP TABLE',
            $ifExists ? 'IF EXISTS' : null,
            $query->quoteName($table),
            $option
        );
    }

    /**
     * Add column
     *
     * @param string $table
     * @param string $column
     * @param string $type
     * @param bool   $allowNull
     * @param mixed  $default
     *
     * @return  string
     */
    public static function addColumn($table, $column, $type = 'text', $allowNull = false, $default = null)
    {
        $query = static::getQuery();

        return static::build(
            'ALTER TABLE',
            $query->quoteName($table),
            'ADD',
            $query->quoteName($column),
            $type,
            $allowNull ? null : 'NOT NULL',
            $default ? 'DEFAULT' . $query->quote($default) : null
        );
    }

    /**
     * changeColumn
     *
     * @param string $table
     * @param string $oldColumn
     * @param string $newColumn
     * @param string $type
     *
     * @return  string
     */
    public static function changeColumn($table, $oldColumn, $newColumn, $type = 'text')
    {
        // No use now
    }

    /**
     * changeColumn
     *
     * @param string $table
     * @param string $oldColumn
     * @param string $newColumn
     *
     * @return  string
     */
    public static function renameColumn(string $table, string $oldColumn, string $newColumn): string
    {
        $query = self::getQuery(true);

        return (string) $query->element(
            'exec sp_rename',
            $query->quote([
                $table . '.' . $oldColumn,
                $newColumn,
                'COLUMN'
            ])
        );
    }

    /**
     * dropColumn
     *
     * @param string $table
     * @param string $column
     *
     * @return  string
     */
    public static function dropColumn($table, $column)
    {
        $query = static::getQuery();

        return static::build(
            'ALTER TABLE',
            $query->quoteName($table),
            'DROP',
            'COLUMN',
            $query->quoteName($column)
        );
    }

    /**
     * addIndex
     *
     * @param string $table
     * @param string $type
     * @param array $columns
     * @param string $name
     *
     * @return string
     */
    public static function addIndex($table, $type, $columns, $name)
    {
        $cols = static::buildIndexDeclare($name, $columns, $table);

        return static::build(
            'CREATE',
            strtoupper($type),
            $cols
        );
    }

    /**
     * buildIndexDeclare
     *
     * @param string $name
     * @param array  $columns
     * @param string $table
     *
     * @return string
     */
    public static function buildIndexDeclare($name, $columns, $table = null)
    {
        $query = static::getQuery();
        $cols = [];

        foreach ((array) $columns as $key => $val) {
            $col = is_numeric($key) ? $val : $key;

            $cols[] = $query->quoteName(trim(explode('(', $col)[0]));
        }

        $cols = '(' . implode(', ', $cols) . ')';

        return static::build(
            $name ? $query->quoteName($name) : null,
            $table ? 'ON ' . $query->quoteName($table) : null,
            $cols
        );
    }

    /**
     * getQuery
     *
     * @param bool $new
     *
     * @return  SqlsrvQuery
     */
    public static function getQuery($new = false)
    {
        if (!static::$query || $new) {
            static::$query = new SqlsrvQuery();
        }

        return static::$query;
    }

    public static function comment(string $type, string $table, string $name, string $comment): string
    {
        $query = self::getQuery(true);

        return (string) $query->element(
            'exec sp_addextendedproperty',
            $query->quote([
                'MS_Description',
                $comment,
                'SCHEMA',
                'dbo',
                'TABLE',
                $table,
                $type,
                $name
            ])
        );
    }
}
