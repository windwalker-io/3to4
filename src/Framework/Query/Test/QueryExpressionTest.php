<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Test;

use Windwalker\Legacy\Database\Test\AbstractQueryTestCase;
use Windwalker\Legacy\Query\Query;
use Windwalker\Legacy\Query\QueryExpression;

/**
 * Test class of QueryExpression
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class QueryExpressionTest extends AbstractQueryTestCase
{
    /**
     * Test instance.
     *
     * @var QueryExpression
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = $this->getInstance();
    }

    /**
     * getInstance
     *
     * @return  QueryExpression
     */
    protected function getInstance()
    {
        return new QueryExpression(new Query());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Method to test buildExpression().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::buildExpression
     * @TODO   Implement testBuildExpression().
     */
    public function testBuildExpression()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getQuery().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::getQuery
     * @TODO   Implement testGetQuery().
     */
    public function testGetQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setQuery().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::setQuery
     * @TODO   Implement testSetQuery().
     */
    public function testSetQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test concatenate().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::concatenate
     * @TODO   Implement testConcatenate().
     */
    public function testConcatenate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test current_timestamp().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::current_timestamp
     * @TODO   Implement testCurrent_timestamp().
     */
    public function testCurrent_timestamp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test year().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::year
     * @TODO   Implement testYear().
     */
    public function testYear()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test month().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::month
     * @TODO   Implement testMonth().
     */
    public function testMonth()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test day().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::day
     * @TODO   Implement testDay().
     */
    public function testDay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test hour().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::hour
     * @TODO   Implement testHour().
     */
    public function testHour()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test minute().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::minute
     * @TODO   Implement testMinute().
     */
    public function testMinute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test second().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::second
     * @TODO   Implement testSecond().
     */
    public function testSecond()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test length().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::length
     * @TODO   Implement testLength().
     */
    public function testLength()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test char_length().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::char_length
     * @TODO   Implement testChar_length().
     */
    public function testChar_length()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test cast_as_char().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Query\QueryExpression::cast_as_char
     * @TODO   Implement testCast_as_char().
     */
    public function testCast_as_char()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
