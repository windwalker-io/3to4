<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query;

use Windwalker\Legacy\Database\Driver\AbstractDatabaseDriver;
use Windwalker\Legacy\Database\Iterator\DataIterator;
use Windwalker\Legacy\Query\Mysql\MysqlQuery;
use Windwalker\Legacy\Query\Query\PreparableInterface;
use Windwalker\Legacy\String\Str;

/**
 * Class AbstractQuery
 *
 * @method mixed loadOne($class = \stdClass::class)
 * @method mixed loadAll($key = null, $class = \stdClass::class)
 * @method mixed loadResult()
 * @method mixed loadColumn()
 * @method mixed execute()
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class Query implements QueryInterface, PreparableInterface, \IteratorAggregate
{
    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = '';

    /**
     * The database driver.
     *
     * @var    AbstractDatabaseDriver|\PDO
     * @since  2.0
     */
    protected $connection = null;

    /**
     * The SQL query (if a direct query string was provided).
     *
     * @var    string
     * @since  2.0
     */
    protected $sql = null;

    /**
     * The query type.
     *
     * @var    string
     * @since  2.0
     */
    protected $type = null;

    /**
     * The query element for a generic query (type = null).
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $element = null;

    /**
     * The select element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $select = null;

    /**
     * The delete element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $delete = null;

    /**
     * The update element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $update = null;

    /**
     * The insert element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $insert = null;

    /**
     * The from element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $from = null;

    /**
     * The join element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $join = null;

    /**
     * The set element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $set = null;

    /**
     * The where element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $where = null;

    /**
     * The group by element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $group = null;

    /**
     * The having element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $having = null;

    /**
     * The column list for an INSERT statement.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $columns = null;

    /**
     * The values list for an INSERT statement.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $values = null;

    /**
     * The order element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $order = null;

    /**
     * The offset for the result set.
     *
     * @var    integer
     * @since  2.0
     */
    protected $offset;

    /**
     * The limit for the result set.
     *
     * @var    integer
     * @since  2.0
     */
    protected $limit;

    /**
     * The query suffix string.
     *
     * @var    QueryElement
     * @since  3.2.7
     */
    protected $suffix;

    /**
     * The auto increment insert field element.
     *
     * @var    object
     * @since  2.0
     */
    protected $autoIncrementField = null;

    /**
     * The call element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $call = null;

    /**
     * The exec element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $exec = null;

    /**
     * The union element.
     *
     * @var    QueryElement
     * @since  2.0
     */
    protected $union = null;

    /**
     * Property dateFormat.
     *
     * @var  string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The null or zero representation of a timestamp for the database driver.  This should be
     * defined in child classes to hold the appropriate value for the engine.
     *
     * @var    string
     * @since  2.0
     */
    protected $nullDate = '0000-00-00 00:00:00';

    /**
     * Property nameQuote.
     *
     * @var  string
     */
    protected $nameQuote = '"';

    /**
     * Property expression.
     *
     * @var  QueryExpression
     */
    protected $expression = null;

    /**
     * Holds key / value pair of bound objects.
     *
     * @var    mixed
     * @since  2.0
     */
    protected $bounded = [];

    /**
     * Property alias.
     *
     * @var string
     */
    protected $alias;

    /**
     * Class constructor.
     *
     * @param   AbstractDatabaseDriver|\PDO|mixed $connection The PDO connection object to help us escape string.
     *
     * @since   2.0
     */
    public function __construct($connection = null)
    {
        $this->connection = $connection ?: ConnectionContainer::getConnection($this->name);
    }

    /**
     * Magic function to convert the query to a string.
     *
     * @return  string    The completed query.
     *
     * @since   2.0
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Convert the query to a string.
     *
     * @return  string    The completed query.
     *
     * @since   2.0
     */
    public function toString()
    {
        $query = '';

        if ($this->sql) {
            return $this->sql;
        }

        switch ($this->type) {
            case 'element':
                $query .= (string) $this->element;
                break;

            case 'select':
                $query .= (string) $this->select;
                $query .= (string) $this->from;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                if ($this->union) {
                    if (!$this->select) {
                        $this->union->setName('()');
                    }

                    $query .= (string) $this->union;
                }

                if ($this->group) {
                    $query .= (string) $this->group;
                }

                if ($this->having) {
                    $query .= (string) $this->having;
                }

                if ($this->order) {
                    $query .= (string) $this->order;
                }

                break;

            case 'delete':
                $query .= (string) $this->delete;
                $query .= (string) $this->from;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                break;

            case 'update':
                $query .= (string) $this->update;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                $query .= (string) $this->set;

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                break;

            case 'insert':
                $query .= (string) $this->insert;

                // Set method
                if ($this->set) {
                    $query .= (string) $this->set;
                } elseif ($this->values) {
                    // Columns-Values method
                    if ($this->columns) {
                        $query .= (string) $this->columns;
                    }

                    $elements = $this->values->getElements();

                    if (!($elements[0] instanceof $this)) {
                        $query .= ' VALUES ';
                    }

                    $query .= (string) $this->values;
                }

                break;

            case 'call':
                $query .= (string) $this->call;
                break;

            case 'exec':
                $query .= (string) $this->exec;
                break;
        }

        // Process Limit
        $query = $this->processLimit($query, $this->limit, $this->offset);

        if ($this->suffix) {
            $query .= ' ' . (string) $this->suffix;
        }

        if ($this->type === 'select' && $this->alias !== null) {
            $query = sprintf('(%s) AS %s', $query, $this->alias);
        }

        return $query;
    }

    /**
     * Get clause  value.
     *
     * @param   string $clause Get query clause.
     *
     * @return  QueryElement|mixed
     */
    public function get($clause)
    {
        return $this->$clause ?? null;
    }

    /**
     * Magic function to get protected variable value
     *
     * @param   string $name The name of the variable.
     *
     * @return  mixed
     *
     * @since   2.0
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Add a single column, or array of columns to the CALL clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * The call method can, however, be called multiple times in the same query.
     *
     * Usage:
     * $query->call('a.*')->call('b.id');
     * $query->call(array('a.*', 'b.id'));
     *
     * @param   mixed $columns A string or an array of field names.
     *
     * @return  static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function call($columns)
    {
        $this->type = 'call';

        if (is_null($this->call)) {
            $this->call = $this->element('CALL', $columns);
        } else {
            $this->call->append($columns);
        }

        return $this;
    }

    /**
     * Clear data from the query or a specific clause of the query.
     *
     * @param   string|array $clause Optionally, the name of the clause to clear, or nothing to clear the whole query.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function clear($clause = null)
    {
        $this->sql = null;

        if (is_array($clause)) {
            foreach ($clause as $clau) {
                $this->clear($clau);
            }

            return $this;
        }

        switch ($clause) {
            case 'select':
                $this->select = null;
                $this->type = null;
                break;

            case 'delete':
                $this->delete = null;
                $this->type = null;
                break;

            case 'update':
                $this->update = null;
                $this->type = null;
                break;

            case 'insert':
                $this->insert = null;
                $this->type = null;
                $this->autoIncrementField = null;
                break;

            case 'from':
                $this->from = null;
                break;

            case 'join':
                $this->join = null;
                break;

            case 'set':
                $this->set = null;
                break;

            case 'where':
                $this->where = null;
                break;

            case 'group':
                $this->group = null;
                break;

            case 'having':
                $this->having = null;
                break;

            case 'order':
                $this->order = null;
                break;

            case 'columns':
                $this->columns = null;
                break;

            case 'values':
                $this->values = null;
                break;

            case 'exec':
                $this->exec = null;
                $this->type = null;
                break;

            case 'call':
                $this->call = null;
                $this->type = null;
                break;

            case 'limit':
                $this->offset = 0;
                $this->limit = 0;
                break;

            case 'suffix':
                $this->suffix = null;
                break;

            case 'union':
                $this->union = null;
                break;

            case 'alias':
                $this->alias = null;
                break;

            default:
                $this->type = null;
                $this->select = null;
                $this->delete = null;
                $this->update = null;
                $this->insert = null;
                $this->from = null;
                $this->join = null;
                $this->set = null;
                $this->where = null;
                $this->group = null;
                $this->having = null;
                $this->order = null;
                $this->columns = null;
                $this->values = null;
                $this->autoIncrementField = null;
                $this->exec = null;
                $this->call = null;
                $this->union = null;
                $this->offset = 0;
                $this->limit = 0;
                $this->suffix = null;
                $this->bounded = [];
                $this->alias = null;
                break;
        }

        return $this;
    }

    /**
     * Adds a column, or array of column names that would be used for an INSERT INTO statement.
     *
     * @param   mixed $columns A column name, or array of column names.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function columns($columns)
    {
        if ($this->columns === null) {
            $this->columns = $this->element('()', $columns);
        } else {
            $this->columns->append($columns);
        }

        return $this;
    }

    /**
     * Returns a PHP date() function compliant date format for the database driver.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the getDateFormat method directly.
     *
     * @return  string  The format string.
     *
     * @since   2.0
     * @throws  \RuntimeException
     */
    public function dateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Creates a formatted dump of the query for debugging purposes.
     *
     * Usage:
     * echo $query->dump();
     *
     * @return  string
     *
     * @since   2.0
     */
    public function dump()
    {
        return '<pre class="windwalker-db-query">' . $this . '</pre>';
    }

    /**
     * Add a table name to the DELETE clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     *
     * Usage:
     * $query->delete('#__a')->where('id = 1');
     *
     * @param   string $table The name of the table to delete from.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function delete($table = null)
    {
        $this->type = 'delete';
        $this->delete = $this->element('DELETE', null);

        if (!empty($table)) {
            $this->from($table);
        }

        return $this;
    }

    /**
     * Method to escape a string for usage in an SQL statement.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the escape method directly.
     *
     * Note that 'e' is an alias for this method as it is in JDatabaseDatabaseDriver.
     *
     * @param   string  $text  The string to be escaped.
     * @param   boolean $extra Optional parameter to provide extra escaping.
     *
     * @return  string  The escaped string.
     *
     * @since   2.0
     * @throws  \RuntimeException if the internal db property is not a valid object.
     */
    public function escape($text, $extra = false)
    {
        if (is_int($text) || is_float($text)) {
            return $text;
        }

        if ($this->connection instanceof AbstractDatabaseDriver) {
            $this->connection->connect();

            $result = substr($this->connection->getConnection()->quote((string) $text), 1, -1);
        } elseif ($this->connection
            && ($this->connection instanceof \PDO || method_exists($this->connection, 'quote'))) {
            $result = substr($this->connection->quote((string) $text), 1, -1);
        } else {
            $result = $this->escapeWithNoConnection($text);
        }

        if ($extra) {
            $extra = ($extra === true) ? '%_' : $extra;

            $result = addcslashes($result, $extra);
        }

        return $result;
    }

    /**
     * If no connection set, we escape it with default function.
     *
     * @param string $text
     *
     * @return  string  The escaped string.
     */
    protected function escapeWithNoConnection($text)
    {
        if (is_int($text) || is_float($text)) {
            return $text;
        }

        $text = str_replace("'", "''", $text);

        return addcslashes($text, "\000\n\r\\\032");
    }

    /**
     * Proxy of escape.
     *
     * @param   string  $text  The string to be escaped.
     * @param   boolean $extra Optional parameter to provide extra escaping.
     *
     * @return  string  The escaped string.
     */
    public function e($text, $extra = false)
    {
        return $this->escape($text, $extra);
    }

    /**
     * Add a single column, or array of columns to the EXEC clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * The exec method can, however, be called multiple times in the same query.
     *
     * Usage:
     * $query->exec('a.*')->exec('b.id');
     * $query->exec(array('a.*', 'b.id'));
     *
     * @param   mixed $columns A string or an array of field names.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function exec($columns)
    {
        $this->type = 'exec';

        if (is_null($this->exec)) {
            $this->exec = $this->element('EXEC', $columns);
        } else {
            $this->exec->append($columns);
        }

        return $this;
    }

    /**
     * Add a table to the FROM clause of the query.
     *
     * Note that while an array of tables can be provided, it is recommended you use explicit joins.
     *
     * Usage:
     * $query->select('*')->from('#__a');
     *
     * @param   mixed  $tables          A string or array of table names.
     *                                  This can be a JDatabaseQuery object (or a child of it) when used
     *                                  as a subquery in FROM clause along with a value for $subQueryAlias.
     * @param   string $subQueryAlias   Alias used when $tables is a JDatabaseQuery.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     * @throws  \InvalidArgumentException
     * @throws  \RuntimeException
     */
    public function from($tables, $subQueryAlias = null)
    {
        if ($subQueryAlias && is_string($subQueryAlias)) {
            if (is_array($tables) || (is_object($tables) && !method_exists($tables, '__toString'))) {
                throw new \InvalidArgumentException('Invalid subquery.');
            }

            $tables = PHP_EOL . '(' . trim((string) $tables) . ') AS ' . $subQueryAlias;
        }

        if ($this->from === null) {
            $this->from = $this->element('FROM', $tables);
        } else {
            $this->from->append($tables);
        }

        return $this;
    }

    /**
     * expression
     *
     * @param string $name
     *
     * @return  string
     */
    public function expression($name)
    {
        $args = func_get_args();

        $expression = $this->getExpression();

        return new ExpressionWrapper(call_user_func_array([$expression, 'buildExpression'], $args));
    }

    /**
     * Alias of expression()
     *
     * @return  mixed
     */
    public function expr()
    {
        return call_user_func_array([$this, 'expression'], func_get_args());
    }

    /**
     * wrapExpression
     *
     * @param mixed $content
     *
     * @return  ExpressionWrapper
     *
     * @since  3.3
     */
    public function wrapExpression($content)
    {
        return new ExpressionWrapper($content);
    }

    /**
     * element
     *
     * @param   string $name     The name of the element.
     * @param   mixed  $elements String or array.
     * @param   string $glue     The glue for elements.
     *
     * @return  QueryElement
     */
    public function element($name, $elements, $glue = ',')
    {
        return new QueryElement($name, $elements, $glue);
    }

    /**
     * ele
     *
     * @param   string $name     The name of the element.
     * @param   mixed  $elements String or array.
     * @param   string $glue     The glue for elements.
     *
     * @return  QueryElement
     */
    public function ele($name, $elements, $glue = ',')
    {
        return $this->element($name, $elements, $glue);
    }

    /**
     * Add a grouping column to the GROUP clause of the query.
     *
     * Usage:
     * $query->group('id');
     *
     * @param   mixed $columns A string or array of ordering columns.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function group($columns)
    {
        if (null === $this->group) {
            $this->group = $this->element('GROUP BY', $columns);
        } else {
            $this->group->append($columns);
        }

        return $this;
    }

    /**
     * A conditions to the HAVING clause of the query.
     *
     * Usage:
     * $query->group('id')->having('COUNT(id) > 5');
     *
     * @param   mixed $conditions A string or array of columns.
     * @param   mixed ...$args    Support more arguments to format query.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function having($conditions)
    {
        if (null === $this->having) {
            $this->having = $this->element('HAVING', [], " AND ");
        }

        $args = func_get_args();

        if (!is_array($conditions) && count($args) > 1) {
            $conditions = call_user_func_array([$this, 'format'], $args);
        }

        $this->having->append($conditions);

        return $this;
    }

    /**
     * Add a single condition, or an array of conditions to the HAVING clause and wrap with OR elements.
     *
     * Usage:
     * $query->orHaving(array('a < 5', 'b > 6'));
     * $query->orHaving('a < 5', 'b > 6');
     * $query->orHaving(function ($query)
     * {
     *     $query->having('a < 5')->having('b > 6');
     * });
     *
     * Result:
     * HAVING ... AND (a < 5 OR b > 6)
     *
     * @param   mixed|callable $conditions A string, array of where conditions or callback to support logic.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   3.0
     */
    public function orHaving($conditions)
    {
        if (is_string($conditions)) {
            $conditions = (array) $conditions;
        }

        if (is_array($conditions)) {
            $args = func_get_args();
            array_shift($args);

            $conditions = array_merge($conditions, $args);

            $this->having((string) new QueryElement('()', $conditions, ' OR '));
        } elseif (is_callable($conditions)) {
            $query = new static($this->connection);

            $query->having = new QueryElement('()', [], ' OR ');

            call_user_func($conditions, $query);

            $this->having((string) $query->having);
        }

        return $this;
    }

    /**
     * Add an INNER JOIN clause to the query.
     *
     * Usage:
     * $query->innerJoin('b ON b.id = a.id')->innerJoin('c ON c.id = b.id');
     *
     * @param array|string $table     The table name with alias.
     * @param array|string $condition The join condition.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function innerJoin($table, $condition = [])
    {
        $this->join('INNER', $table, $condition);

        return $this;
    }

    /**
     * Add a table name to the INSERT clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     *
     * Usage:
     * $query->insert('#__a')->set('id = 1');
     * $query->insert('#__a')->columns('id, title')->values('1,2')->values('3,4');
     * $query->insert('#__a')->columns('id, title')->values(array('1,2', '3,4'));
     *
     * @param   mixed   $table          The name of the table to insert data into.
     * @param   boolean $incrementField The name of the field to auto increment.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function insert($table, $incrementField = false)
    {
        $this->type = 'insert';
        $this->insert = $this->element('INSERT INTO', $table);
        $this->autoIncrementField = $incrementField;

        return $this;
    }

    /**
     * Add a JOIN clause to the query.
     *
     * Usage:
     * $query->join('INNER', 'table AS b', 'b.id = a.id');
     *
     * @param   string       $type       The type of join. This string is prepended to the JOIN keyword.
     * @param   string       $table      The table name with alias.
     * @param   string|array $conditions A string or array of conditions.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function join($type, $table, $conditions = [])
    {
        if (null === $this->join) {
            $this->join = [];
        }

        if (is_string($table) || $table instanceof static) {
            $conditions = is_array($conditions) ? $conditions : [$conditions];

            $conditions = array_map([$this, 'applyFormat'], $conditions);

            $table .= ($conditions ? ' ON ' . implode(' AND ', $conditions) : '');
        }

        $this->join[] = $this->element(strtoupper($type) . ' JOIN', (array) $table);

        return $this;
    }

    /**
     * Add a LEFT JOIN clause to the query.
     *
     * Usage:
     * $query->leftJoin('b ON b.id = a.id')->leftJoin('c ON c.id = b.id');
     *
     * @param array|string $table     The table name with alias.
     * @param array|string $condition The join condition.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function leftJoin($table, $condition = [])
    {
        $this->join('LEFT', $table, $condition);

        return $this;
    }

    /**
     * Get the null or zero representation of a timestamp for the database driver.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the nullDate method directly.
     *
     * Usage:
     * $query->where('modified_date <> '.$query->nullDate());
     *
     * @param   boolean $quoted Optionally wraps the null date in database quotes (true by default).
     *
     * @return  string  Null or zero representation of a timestamp.
     *
     * @since   2.0
     * @throws  \RuntimeException
     */
    public function nullDate($quoted = true)
    {
        return $quoted ? $this->quote($this->nullDate) : $this->nullDate;
    }

    /**
     * Add a ordering column to the ORDER clause of the query.
     *
     * Usage:
     * $query->order('foo')->order('bar');
     * $query->order(array('foo','bar'));
     *
     * @param   mixed $columns A string or array of ordering columns.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function order($columns)
    {
        if (null === $this->order) {
            $this->order = $this->element('ORDER BY', $columns);
        } else {
            $this->order->append($columns);
        }

        return $this;
    }

    /**
     * Sets the offset and limit for the result set, if the database driver supports it.
     *
     * Usage:
     * $query->setLimit(100, 0); (retrieve 100 rows, starting at first record)
     * $query->setLimit(50, 50); (retrieve 50 rows, starting at 50th record)
     *
     * @param   integer $limit  The limit for the result set
     * @param   integer $offset The offset for the result set
     *
     * @return static Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function limit($limit = null, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset.
     *
     * @param   string  $query  The query in string format
     * @param   integer $limit  The limit for the result set
     * @param   integer $offset The offset for the result set
     *
     * @return string
     * @since   2.0
     */
    public function processLimit($query, $limit, $offset = null)
    {
        if ($limit && $offset === null) {
            $query .= ' LIMIT ' . (int) $limit;
        } elseif ($limit) {
            $query .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        }

        return $query;
    }

    /**
     * Add an OUTER JOIN clause to the query.
     *
     * Usage:
     * $query->outerJoin('b ON b.id = a.id')->outerJoin('c ON c.id = b.id');
     *
     * @param array|string $table     The table name with alias.
     * @param array|string $condition The join condition.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function outerJoin($table, $condition = [])
    {
        $this->join('OUTER', $table, $condition);

        return $this;
    }

    /**
     * Method to quote and optionally escape a string to database requirements for insertion into the database.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the quote method directly.
     *
     * Note that 'q' is an alias for this method as it is in DatabaseDriver.
     *
     * Usage:
     * $query->quote('fulltext');
     * $query->q('fulltext');
     * $query->q(array('option', 'fulltext'));
     *
     * @param   mixed   $text   A string or an array of strings to quote.
     * @param   boolean $escape True to escape the string, false to leave it unchanged.
     *
     * @return  string  The quoted input string.
     *
     * @since   2.0
     * @throws  \RuntimeException if the internal db property is not a valid object.
     */
    public function quote($text, $escape = true)
    {
        if (null === $text) {
            return 'NULL';
        }

        if ($text instanceof ExpressionWrapper) {
            return $text;
        }

        if (is_array($text) || is_object($text)) {
            $text = (array) $text;

            foreach ($text as $k => $v) {
                $text[$k] = $this->quote($v, $escape);
            }

            return $text;
        }

        return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
    }

    /**
     * Proxy of quote().
     *
     * @param   mixed   $text   A string or an array of strings to quote.
     * @param   boolean $escape True to escape the string, false to leave it unchanged.
     *
     * @return  string
     */
    public function q($text, $escape = true)
    {
        return $this->quote($text, $escape);
    }

    /**
     * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
     * risks and reserved word conflicts.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the quoteName method directly.
     *
     * Note that 'qn' is an alias for this method as it is in DatabaseDriver.
     *
     * Usage:
     * $query->quoteName('#__a');
     * $query->qn('#__a');
     *
     * @param   mixed $name   The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
     *                        Each type supports dot-notation name.
     *
     * @return  mixed  The quote wrapped name, same type of $name.
     *
     * @since   2.0
     * @throws  \RuntimeException if the internal db property is not a valid object.
     */
    public function quoteName($name)
    {
        if ($name instanceof ExpressionWrapper) {
            return $name;
        }

        if (is_string($name)) {
            $pos = stripos($name, ' AS ');
            $quotedAlias = '';

            if ($pos !== false) {
                $alias = substr($name, $pos + 4);
                $name = substr($name, 0, $pos);

                $quotedAlias = $this->quoteNameStr([$alias]);
            }

            if (strpos($name, '->') !== false) {
                $quotedName = $this->jsonSelector($name);
            } else {
                $quotedName = $this->quoteNameStr(explode('.', $name));
            }

            return $quotedName . ($quotedAlias ? ' AS ' . $quotedAlias : '');
        }

        if (is_array($name) || is_object($name)) {
            $fin = [];

            foreach ((array) $name as $n) {
                $fin[] = $this->quoteName($n);
            }

            return $fin;
        }

        return $name;
    }

    /**
     * Proxy of quoteName().
     *
     * @param   mixed $name   The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
     *                        Each type supports dot-notation name.
     *
     * @return  mixed  The quote wrapped name, same type of $name.
     */
    public function qn($name)
    {
        return $this->quoteName($name);
    }

    /**
     * Quote strings coming from quoteName call.
     *
     * @param   array $strArr Array of strings coming from quoteName dot-explosion.
     *
     * @return  string  Dot-imploded string of quoted parts.
     *
     * @since   2.0
     */
    protected function quoteNameStr($strArr)
    {
        $parts = [];
        $q = $this->nameQuote;

        foreach ($strArr as $part) {
            if (null === $part) {
                continue;
            }

            if (strlen($q) === 1) {
                $parts[] = $q . $part . $q;
            } else {
                $parts[] = $q[0] . $part . $q[1];
            }
        }

        return implode('.', $parts);
    }

    /**
     * Add a RIGHT JOIN clause to the query.
     *
     * Usage:
     * $query->rightJoin('b ON b.id = a.id')->rightJoin('c ON c.id = b.id');
     *
     * @param array|string $table     The table name with alias.
     * @param array|string $condition The join condition.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function rightJoin($table, $condition = [])
    {
        $this->join('RIGHT', $table, $condition);

        return $this;
    }

    /**
     * Add a single column, or array of columns to the SELECT clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * The select method can, however, be called multiple times in the same query.
     *
     * Usage:
     * $query->select('a.*')->select('b.id');
     * $query->select(array('a.*', 'b.id'));
     *
     * @param   mixed $columns A string or an array of field names.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function select($columns)
    {
        $this->type = 'select';

        if (null === $this->select) {
            $this->select = $this->element('SELECT', $columns);
        } else {
            $this->select->append($columns);
        }

        return $this;
    }

    /**
     * Add a single condition string, or an array of strings to the SET clause of the query.
     *
     * Usage:
     * $query->set('a = 1')->set('b = 2');
     * $query->set(array('a = 1', 'b = 2');
     *
     * @param   mixed  $conditions   A string or array of string conditions.
     * @param   string $glue         The glue by which to join the condition strings. Defaults to ,.
     *                               Note that the glue is set on first use and cannot be changed.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function set($conditions, $glue = ',')
    {
        if (null === $this->set) {
            $glue = strtoupper($glue);
            $this->set = $this->element('SET', $conditions, PHP_EOL . "\t$glue ");
        } else {
            $this->set->append($conditions);
        }

        return $this;
    }

    /**
     * Allows a direct query to be provided to the database driver's setQuery() method, but still allow queries
     * to have bounded variables.
     *
     * Usage:
     * $query->setQuery('select * from #__users');
     *
     * @param   mixed $sql A SQL query string or DatabaseQuery object
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function setQuery($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Add a table name to the UPDATE clause of the query.
     *
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     *
     * Usage:
     * $query->update('#__foo')->set(...);
     *
     * @param   string $table A table to update.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function update($table)
    {
        $this->type = 'update';
        $this->update = $this->element('UPDATE', $table);

        return $this;
    }

    /**
     * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
     *
     * Usage:
     * $query->values('1,2,3')->values('4,5,6');
     * $query->values(array('1,2,3', '4,5,6'));
     *
     * @param   string|array $values A single tuple, or array of tuples.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function values($values)
    {
        $values = (array) $values;

        foreach ($values as &$value) {
            if (is_array($value) || is_object($value)) {
                $value = implode(',', (array) $value);
            }
        }

        if (null === $this->values) {
            $this->values = $this->element('()', $values, '),' . PHP_EOL . '(');
        } else {
            $this->values->append($values);
        }

        return $this;
    }

    /**
     * Add a single condition, or an array of conditions to the WHERE clause of the query.
     *
     * Usage:
     * $query->where('a = 1')->where('b = 2');
     * $query->where(array('a = 1', 'b = 2'));
     * $query->where('%n = %q', 'a', 'b');
     *
     * @param   mixed $conditions A string or array of where conditions.
     * @param   mixed ...$args    Support more arguments to format query.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function where($conditions)
    {
        if (null === $this->where) {
            $this->where = $this->element('WHERE', [], ' AND ');
        }

        $args = func_get_args();

        if (!is_array($conditions) && count($args) > 1) {
            $conditions = call_user_func_array([$this, 'format'], $args);
        } elseif (count($args) === 1) {
            $conditions = is_array($conditions) ? $conditions : [$conditions];

            $conditions = array_map([$this, 'applyFormat'], $conditions);
        }

        $this->where->append($conditions);

        return $this;
    }

    /**
     * whereIn
     *
     * @param string $column
     * @param array  $values
     *
     * @return  Query
     *
     * @since  3.4.5
     */
    public function whereIn($column, array $values)
    {
        if ($values === []) {
            throw new \InvalidArgumentException('Values for IN() is empty');
        }

        return $this->where('%n %r', $column, new QueryElement('IN()', $this->quote($values)));
    }

    /**
     * whereNotIn
     *
     * @param string $column
     * @param array  $values
     *
     * @return  Query
     *
     * @since  3.4.5
     */
    public function whereNotIn($column, array $values)
    {
        if ($values === []) {
            throw new \InvalidArgumentException('Values for NOT IN() is empty');
        }

        return $this->where('%n %r', $column, new QueryElement('NOT IN()', $this->quote($values)));
    }

    /**
     * Add a single condition, or an array of conditions to the WHERE clause and wrap with OR elements.
     *
     * Usage:
     * $query->orWhere(array('a < 5', 'b > 6'));
     * $query->orWhere('a < 5', 'b > 6');
     * $query->orWhere(function ($query)
     * {
     *     $query->where('a < 5')->where('b > 6');
     * });
     *
     * Result:
     * WHERE ... AND (a < 5 OR b > 6)
     *
     * @param   mixed|callable $conditions A string, array of where conditions or callback to support logic.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   3.0
     */
    public function orWhere($conditions)
    {
        if (is_string($conditions)) {
            $conditions = (array) $conditions;
        }

        if (is_array($conditions)) {
            $args = func_get_args();
            array_shift($args);

            $conditions = array_merge($conditions, $args);

            if ($conditions !== []) {
                $this->where(new QueryElement('()', $conditions, ' OR '));
            }
        } elseif (is_callable($conditions)) {
            $query = new static($this->connection);

            $query->where = new QueryElement('()', [], ' OR ');

            $conditions($query);

            if ($query->where) {
                $this->where((string) $query->where);
            }
        }

        return $this;
    }

    /**
     * Method to provide deep copy support to nested objects and arrays when cloning.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function __clone()
    {
        foreach (get_object_vars($this) as $k => $v) {
            if ($k === 'connection') {
                continue;
            }

            if (is_object($v) || is_array($v)) {
                $this->{$k} = unserialize(serialize($v));
            }
        }
    }

    /**
     * Add a query to UNION with the current query.
     * Multiple unions each require separate statements and create an array of unions.
     *
     * Usage:
     * $query->union('SELECT name FROM  #__foo')
     * $query->union('SELECT name FROM  #__foo','distinct')
     * $query->union(array('SELECT name FROM  #__foo', 'SELECT name FROM  #__bar'))
     *
     * @param   mixed   $query    The Query object or string to union.
     * @param   boolean $distinct True to only return distinct rows from the union.
     *
     * @return  static    The Query object on success or boolean false on failure.
     *
     * @since   2.0
     */
    public function union($query, $distinct = false)
    {
        $this->type = 'select';

        // Clear any ORDER BY clause in UNION query
        // See http://dev.mysql.com/doc/refman/5.0/en/union.html
        if (null !== $this->order) {
            $this->clear('order');
        }

        // Set up the DISTINCT flag, the name with parentheses, and the glue.
        if ($distinct) {
            $name = 'UNION DISTINCT()';
            $glue = ')' . PHP_EOL . 'UNION DISTINCT (';
        } else {
            $name = 'UNION()';
            $glue = ')' . PHP_EOL . 'UNION (';
        }

        // Get the QueryElement if it does not exist
        if (null === $this->union) {
            $this->union = $this->element($name, $query, "$glue");
        } else // Otherwise append the second UNION.
        {
            $this->union->append($query);
        }

        return $this;
    }

    /**
     * Add a query to UNION DISTINCT with the current query. Simply a proxy to Union with the Distinct clause.
     *
     * Usage:
     * $query->unionDistinct('SELECT name FROM  #__foo')
     *
     * @param   mixed $query The Query object or string to union.
     *
     * @return  mixed   The Query object on success or boolean false on failure.
     *
     * @since   2.0
     */
    public function unionDistinct($query)
    {
        $distinct = true;

        // Apply the distinct flag to the union.
        return $this->union($query, $distinct);
    }

    /**
     * Add a query to UNION ALL with the current query.
     * Multiple unions each require separate statements and create an array of unions.
     *
     * Usage:
     * $query->unionAll('SELECT name FROM  #__foo')
     * $query->unionAll(array('SELECT name FROM  #__foo','SELECT name FROM  #__bar'))
     *
     * @param   mixed $query The Query object or string to union.
     *
     * @return  mixed  The Query object on success or boolean false on failure.
     *
     * @see     union
     *
     * @since   2.0
     */
    public function unionAll($query)
    {
        $this->type = 'select';

        $glue = ')' . PHP_EOL . 'UNION ALL (';

        // Get the JDatabaseQueryElement if it does not exist
        if (null === $this->union) {
            $this->union = $this->element('UNION ALL ()', $query, $glue);
        } else {
            // Otherwise append the second UNION.
            $this->union->append($query);
        }

        return $this;
    }

    /**
     * Suffix string to query.
     *
     * @param string $string
     *
     * @return  static
     *
     * @since   3.2.7
     */
    public function suffix($string)
    {
        if (null === $this->suffix) {
            $this->suffix = $this->element('', [], ' ');
        }

        $args = func_get_args();

        if (!is_array($string) && count($args) > 1) {
            $string = call_user_func_array([$this, 'format'], $args);
        }

        $this->suffix->append($string);

        return $this;
    }

    /**
     * Add FOR UPDATE after query string.
     *
     * @return  static
     *
     * @since   3.2.7
     */
    public function forUpdate()
    {
        return $this->suffix('FOR UPDATE');
    }

    /**
     * Method to set property alias
     *
     * @param   string $alias
     *
     * @return  static  Return self to support chaining.
     *
     * @since   3.4.8
     */
    public function alias($alias)
    {
        $this->alias = (string) $alias;

        return $this;
    }

    /**
     * applyFormat
     *
     * @param string|FormatWrapper $format
     *
     * @return  mixed|string
     *
     * @since  3.5.12
     */
    protected function applyFormat($format)
    {
        if (!$format instanceof FormatWrapper) {
            return $format;
        }

        return $this->format(...$format->all());
    }

    /**
     * Find and replace sprintf-like tokens in a format string.
     * Each token takes one of the following forms:
     *     %%       - A literal percent character.
     *     %[t]     - Where [t] is a type specifier.
     *     %[n]$[x] - Where [n] is an argument specifier and [t] is a type specifier.
     *
     * Types:
     * a - Numeric: Replacement text is coerced to a numeric type but not quoted or escaped.
     * e - Escape: Replacement text is passed to $this->escape().
     * E - Escape (extra): Replacement text is passed to $this->escape() with true as the second argument.
     * n - Name Quote: Replacement text is passed to $this->quoteName().
     * q - Quote: Replacement text is passed to $this->quote().
     * Q - Quote (no escape): Replacement text is passed to $this->quote() with false as the second argument.
     * r - Raw: Replacement text is used as-is. (Be careful)
     *
     * Date Types:
     * - Replacement text automatically quoted (use uppercase for Name Quote).
     * - Replacement text should be a string in date format or name of a date column.
     * y/Y - Year
     * m/M - Month
     * d/D - Day
     * h/H - Hour
     * i/I - Minute
     * s/S - Second
     *
     * Invariable Types:
     * - Takes no argument.
     * - Argument index not incremented.
     * t - Replacement text is the result of $this->currentTimestamp().
     * z - Replacement text is the result of $this->nullDate(false).
     * Z - Replacement text is the result of $this->nullDate(true).
     *
     * Usage:
     * $query->format('SELECT %1$n FROM %2$n WHERE %3$n = %4$a', 'foo', '#__foo', 'bar', 1);
     * Returns: SELECT `foo` FROM `#__foo` WHERE `bar` = 1
     *
     * Notes:
     * The argument specifier is optional but recommended for clarity.
     * The argument index used for unspecified tokens is incremented only when used.
     *
     * @param   string $format The formatting string.
     *
     * @return  string  Returns a string produced according to the formatting string.
     *
     * @note    This method is a modified version from Joomla.
     *
     * @since   2.0
     */
    public function format($format)
    {
        $query = $this;
        $args = array_slice(func_get_args(), 1);
        array_unshift($args, null);

        $expression = $this->getExpression();

        $i = 1;
        $func = function ($match) use ($query, $args, &$i, $expression) {
            if (isset($match[6]) && $match[6] === '%') {
                return '%';
            }

            // No argument required, do not increment the argument index.
            switch ($match[5]) {
                case 't':
                    return $expression->current_timestamp();
                    break;

                case 'z':
                    return $query->nullDate(false);
                    break;

                case 'Z':
                    return $query->nullDate(true);
                    break;
            }

            // Increment the argument index only if argument specifier not provided.
            $index = is_numeric($match[4]) ? (int) $match[4] : $i++;

            if (!$index || !isset($args[$index])) {
                $replacement = '';
            } else {
                $replacement = $args[$index];
            }

            switch ($match[5]) {
                case 'a':
                    return 0 + $replacement;
                    break;

                case 'e':
                    return $query->escape($replacement);
                    break;

                case 'E':
                    return $query->escape($replacement, true);
                    break;

                case 'n':
                    return $query->quoteName($replacement);
                    break;

                case 'q':
                    return $query->quote($replacement);
                    break;

                case 'Q':
                    return $query->quote($replacement, false);
                    break;

                case 'r':
                    return $replacement;
                    break;

                // Dates
                case 'y':
                    return $expression->year($query->quote($replacement));
                    break;

                case 'Y':
                    return $expression->year($query->quoteName($replacement));
                    break;

                case 'm':
                    return $expression->month($query->quote($replacement));
                    break;

                case 'M':
                    return $expression->month($query->quoteName($replacement));
                    break;

                case 'd':
                    return $expression->day($query->quote($replacement));
                    break;

                case 'D':
                    return $expression->day($query->quoteName($replacement));
                    break;

                case 'h':
                    return $expression->hour($query->quote($replacement));
                    break;

                case 'H':
                    return $expression->hour($query->quoteName($replacement));
                    break;

                case 'i':
                    return $expression->minute($query->quote($replacement));
                    break;

                case 'I':
                    return $expression->minute($query->quoteName($replacement));
                    break;

                case 's':
                    return $expression->second($query->quote($replacement));
                    break;

                case 'S':
                    return $expression->second($query->quoteName($replacement));
                    break;
            }

            return '';
        };

        /**
         * Regexp to find an replace all tokens.
         * Matched fields:
         * 0: Full token
         * 1: Everything following '%'
         * 2: Everything following '%' unless '%'
         * 3: Argument specifier and '$'
         * 4: Argument specifier
         * 5: Type specifier
         * 6: '%' if full token is '%%'
         */
        return preg_replace_callback('#%(((([\d]+)\$)?([aeEnqQryYmMdDhHiIsStzZ]))|(%))#', $func, $format);
    }

    /**
     * getName
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getExpression
     *
     * @return  QueryExpression
     */
    public function getExpression()
    {
        if ($this->expression) {
            return $this->expression;
        }

        $class = __NAMESPACE__ . '\\' . ucfirst($this->getName()) . '\\' . ucfirst($this->getName()) . 'Expression';

        if (!class_exists($class)) {
            $class = __NAMESPACE__ . '\\' . 'QueryExpression';
        }

        return $this->expression = new $class($this);
    }

    /**
     * setExpression
     *
     * @param   \Windwalker\Legacy\Query\QueryExpression $expression
     *
     * @return  static  Return self to support chaining.
     */
    public function setExpression(QueryExpression $expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Method to get property Connection
     *
     * @return  AbstractDatabaseDriver|\PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Method to set property connection
     *
     * @param   AbstractDatabaseDriver|\PDO $connection
     *
     * @return  static  Return self to support chaining.
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param   string|integer|array  $key           The key that will be used in your SQL query to reference the
     *                                               value. Usually of the form ':key', but can also be an integer.
     * @param   mixed                &$value         The value that will be bound. The value is passed by reference to
     *                                               support output parameters such as those possible with stored
     *                                               procedures.
     * @param   integer               $dataType      Constant corresponding to a SQL datatype.
     * @param   integer               $length        The length of the variable. Usually required for OUTPUT
     *                                               parameters.
     * @param   array                 $driverOptions Optional driver options to be used.
     *
     * @return  static
     *
     * @since   2.0
     */
    public function bind(
        $key = null,
        $value = null,
        $dataType = null,
        $length = null,
        $driverOptions = null
    ) {
        return $this->bindRef($key, $value, $dataType, $length, $driverOptions);
    }

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param string|integer|array $key The key that will be used in your SQL query to reference the value.
     *                                          Usually of the form ':key', but can also be an integer.
     * @param mixed                &$value The value that will be bound. The value is passed by reference to
     *                                          support output parameters such as those possible with stored
     *                                          procedures.
     * @param integer $dataType Constant corresponding to a SQL datatype.
     * @param integer $length The length of the variable. Usually required for OUTPUT parameters.
     * @param array $driverOptions Optional driver options to be used.
     *
     * @return  static
     *
     * @since   3.5.5
     */
    public function bindRef(
        $key = null,
        &$value = null,
        $dataType = null,
        $length = 0,
        $driverOptions = null
    ) {
        // If is array, loop for all elements.
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->bind($k, $v, $dataType, $length, $driverOptions);
            }

            return $this;
        }

        if ($dataType === null) {
            if (is_numeric($value) && strpos((string) $value, '.') === false) {
                $dataType = \PDO::PARAM_INT;
            } else {
                $dataType = \PDO::PARAM_STR;
            }
        }

        $obj = new \stdClass();

        $obj->value = &$value;
        $obj->dataType = $dataType;
        $obj->length = $length;
        $obj->driverOptions = $driverOptions;

        // Case 3: Simply add the Key/Value into the bounded array
        $this->bounded[$key] = $obj;

        return $this;
    }

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param array           &$values          The value that will be bound. The value is passed by reference to
     *                                          support output parameters such as those possible with stored
     *                                          procedures.
     * @param integer          $dataType        Constant corresponding to a SQL datatype.
     * @param integer          $length          The length of the variable. Usually required for OUTPUT parameters.
     * @param array            $driverOptions   Optional driver options to be used.
     *
     * @return  static
     *
     * @since   3.5.5
     */
    public function bindValues(array &$values, $dataType = null, $length = 0, $driverOptions = null)
    {
        foreach ($values as $k => &$v) {
            $this->bindRef($k, $v);
        }

        return $this;
    }

    /**
     * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then
     * that item is returned.
     *
     * @param   mixed $key The bounded variable key to retrieve.
     *
     * @return  array|null
     *
     * @since   2.0
     */
    public function &getBounded($key = null)
    {
        if (empty($key)) {
            return $this->bounded;
        }

        if (isset($this->bounded[$key])) {
            return $this->bounded[$key];
        }

        return null;
    }

    /**
     * resetBounded
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function resetBounded()
    {
        $this->bounded = [];

        return $this;
    }

    /**
     * unbind
     *
     * @param string|array $keys
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function unbind($keys)
    {
        $keys = (array) $keys;

        $this->bounded = array_diff_key($this->bounded, array_flip($keys));

        return $this;
    }

    /**
     * Method to get property DateFormat
     *
     * @return  string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Method to set property dateFormat
     *
     * @param   string $dateFormat
     *
     * @return  static  Return self to support chaining.
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Method to get property NullDate
     *
     * @return  string
     */
    public function getNullDate()
    {
        return $this->nullDate;
    }

    /**
     * Method to set property nullDate
     *
     * @param   string $nullDate
     *
     * @return  static  Return self to support chaining.
     */
    public function setNullDate($nullDate)
    {
        $this->nullDate = $nullDate;

        return $this;
    }

    /**
     * getValidValue
     *
     * @param string $value
     * @param bool   $allowExpression
     *
     * @return  float|int|string
     */
    public function validValue($value, $allowExpression = false)
    {
        if ($value === null) {
            return 'NULL';
        }

        if ($value instanceof ExpressionWrapper) {
            return $value->getContent();
        }

        if ($value instanceof FormatWrapper) {
            return $this->quote($this->applyFormat($value));
        }

        if (is_float($value) || is_double($value)) {
            return (double) $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if ($allowExpression && $this->getExpression()->isExpression($value)) {
            return $value;
        }

        return $this->quote($value);
    }

    /**
     * getValidDatetime
     *
     * @param   string $string
     *
     * @return  string
     * @throws  \Exception
     */
    public function validDatetime($string)
    {
        $date = new \DateTime($string);

        return $date->format($this->getDateFormat());
    }

    /**
     * getBuilder
     *
     * @return  QueryGrammarInterface
     */
    public function getGrammar()
    {
        return AbstractQueryGrammar::getInstance($this->getName());
    }

    /**
     * pipe
     *
     * @param callable $handler
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function pipe(callable $handler): self
    {
        $handler($this);

        return $this;
    }

    /**
     * Unsetting PDO connection before going to sleep (this is needed if the query gets serialized)
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['connection']);
    }

    /**
     * Trying to re-retrieve the pdo connection after waking up
     */
    public function __wakeup()
    {
        if ($this->name) {
            $this->connection = ConnectionContainer::getConnection($this->name);
        }
    }

    /**
     * getIterator
     *
     * @param string $class
     *
     * @return  \Traversable|DataIterator
     *
     * @since  3.5.19
     */
    #[\ReturnTypeWillChange]
    public function getIterator(string $class = 'stdClass')
    {
        if (!$this->connection instanceof AbstractDatabaseDriver) {
            throw new \LogicException(
                'Loading data from Query must inject AbstractDatabaseDriver object into it.'
            );
        }

        return $this->connection->prepare($this)->getIterator($class);
    }

    /**
     * parseJsonExtract
     *
     * @param  string  $expr
     *
     * @return  string
     *
     * @since  3.5.21
     */
    public function jsonSelector(string $expr): string
    {
        $unQuoteLast = strpos($expr, '->>') !== false;

        $paths = array_filter(array_map('trim', preg_split('/->+/', $expr)), 'strlen');
        $paths = array_map(
            function ($segment) {
                return trim($segment, "\"'");
            },
            $paths
        );

        $column = array_shift($paths);

        return $this->getGrammar()::buildJsonSelector($column, $paths, $unQuoteLast);
    }

    /**
     * __call
     *
     * @param string $name
     * @param array  $args
     *
     * @return  mixed
     *
     * @since  3.5.12
     */
    public function __call(string $name, array $args)
    {
        $dbMethods = [
            'loadOne',
            'loadAll',
            'loadResult',
            'loadColumn',
            'execute'
        ];

        if (in_array($name, $dbMethods, true)) {
            if (!$this->connection instanceof AbstractDatabaseDriver) {
                throw new \LogicException(
                    'Loading data from Query must inject AbstractDatabaseDriver object into it.'
                );
            }

            if ($name === 'execute') {
                return $this->connection->$name($this);
            }

            return $this->connection->prepare($this)->$name(...$args);
        }

        throw new \BadMethodCallException(sprintf(
            'Method %s not found',
            $name
        ));
    }
}
