<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Query;

/**
 * Windwalker Query Preparable Interface.
 *
 * Adds bind/unbind methods as well as a getBounded() method
 * to retrieve the stored bounded variables on demand prior to
 * query execution.
 *
 * @since  2.0
 */
interface PreparableInterface
{
    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param   string|integer  $key            The key that will be used in your SQL query to reference the value.
     *                                          Usually of the form ':key', but can also be an integer.
     * @param   mixed          &$value          The value that will be bound. The value is passed by reference to
     *                                          support output parameters such as those possible with stored
     *                                          procedures.
     * @param   integer         $dataType       Constant corresponding to a SQL datatype.
     * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
     * @param   array           $driverOptions  Optional driver options to be used.
     *
     * @return  static
     *
     * @since   2.0
     */
    public function bind($key = null, $value = null, $dataType = \PDO::PARAM_STR, $length = 0, $driverOptions = null);

    /**
     * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then
     * that item is returned.
     *
     * @param   mixed $key The bounded variable key to retrieve.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function &getBounded($key = null);

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param   string|integer  $key            The key that will be used in your SQL query to reference the value.
     *                                          Usually of the form ':key', but can also be an integer.
     * @param   mixed          &$value          The value that will be bound. The value is passed by reference to
     *                                          support output parameters such as those possible with stored
     *                                          procedures.
     * @param   integer         $dataType       Constant corresponding to a SQL datatype.
     * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
     * @param   array           $driverOptions  Optional driver options to be used.
     *
     * @return  static
     *
     * @since   3.5.5
     */
    public function bindRef(
        $key = null,
        &$value = null,
        $dataType = \PDO::PARAM_STR,
        $length = 0,
        $driverOptions = null
    );

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param   array           &$values        The value that will be bound. The value is passed by reference to
     *                                          support output parameters such as those possible with stored
     *                                          procedures.
     * @param   integer         $dataType       Constant corresponding to a SQL datatype.
     * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
     * @param   array           $driverOptions  Optional driver options to be used.
     *
     * @return  static
     *
     * @since   3.5.5
     */
    public function bindValues(
        array &$values,
        $dataType = \PDO::PARAM_STR,
        $length = 0,
        $driverOptions = null
    );
}
