<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Test\Cli;

use Windwalker\Legacy\IO\Cli\Output\CliOutput;

/**
 * Test class of CliOutput
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class CliOutputTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var CliOutput
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
        $this->instance = new CliOutput();
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
     * Method to test out().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Output\CliOutput::out
     * @TODO   Implement testOut().
     */
    public function testOut()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test err().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Output\CliOutput::err
     * @TODO   Implement testErr().
     */
    public function testErr()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setProcessor().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Output\CliOutput::setProcessor
     * @TODO   Implement testSetProcessor().
     */
    public function testSetProcessor()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getProcessor().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Output\CliOutput::getProcessor
     * @TODO   Implement testGetProcessor().
     */
    public function testGetProcessor()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
