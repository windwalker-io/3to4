<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Database\Test\Mysql;

use Windwalker\Legacy\Database\Driver\Mysql\MysqlTransaction;

/**
 * Test class of MysqlTransaction
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class MysqlTransactionTest extends AbstractMysqlTestCase
{
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

    /**
     * Method to test getNested().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Command\AbstractTransaction::getNested
     */
    public function testGetNested()
    {
        $tran = new MysqlTransaction($this->db, false);

        $this->assertFalse($tran->getNested());
    }

    /**
     * Method to test setNested().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Command\AbstractTransaction::setNested
     */
    public function testSetNested()
    {
        $tran = new MysqlTransaction($this->db);

        $tran->setNested(false);

        $this->assertFalse($tran->getNested());
    }

    /**
     * Method to test start().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Driver\Mysql\MysqlTransaction::start
     * @covers \Windwalker\Legacy\Database\Driver\Mysql\MysqlTransaction::rollback
     */
    public function testTransactionRollback()
    {
        $table = '#__flower';

        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('A', '', ''), ('B', '', ''), ('C', '', '')";

        $tran = $this->db->getTransaction()->start();

        $this->db->setQuery($sql)->execute();

        $tran->rollback();

        $result = $this->db->getReader('SELECT title FROM #__flower WHERE title = "A"')->loadResult();

        $this->assertFalse($result);
    }

    /**
     * Method to test start().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Driver\Mysql\MysqlTransaction::start
     * @covers \Windwalker\Legacy\Database\Driver\Mysql\MysqlTransaction::commit
     */
    public function testTransactionCommit()
    {
        $table = '#__flower';

        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('A', '', ''), ('B', '', ''), ('C', '', '')";

        $tran = $this->db->getTransaction()->start();

        $this->db->setQuery($sql)->execute();

        $tran->commit();

        $result = $this->db->getReader('SELECT title FROM #__flower WHERE title = "A"')->loadResult();

        $this->assertEquals('A', $result);
    }

    /**
     * testTransactionNested
     *
     * @return  void
     *
     * @since  3.5
     */
    public function testTransactionNested()
    {
        $table = '#__flower';

        // Level 1
        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('D', '', '')";

        $tran = $this->db->getTransaction()->start();

        $this->db->execute($sql);

        // Level 2
        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('E', '', '')";

        $tran = $tran->start();

        $this->db->execute($sql);

        $tran->rollback();
        $tran->commit();

        $result = $this->db->getReader('SELECT title FROM #__flower WHERE title = \'D\'')->loadResult();
        $this->assertEquals('D', $result);

        $result2 = $this->db->getReader('SELECT title FROM #__flower WHERE title = \'E\'')->loadResult();
        $this->assertNotEquals('E', $result2);
    }

    /**
     * Method to test getDriver().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Command\AbstractTransaction::getDriver
     */
    public function testGetDriver()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setDriver().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Database\Command\AbstractTransaction::setDriver
     * @TODO   Implement testSetDriver().
     */
    public function testSetDriver()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
