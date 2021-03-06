<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Query\Test;

/**
 * QueryTestTrait
 *
 * @since  3.2.7
 */
trait QueryTestTrait
{
    /**
     * testSuffix
     *
     * @return  void
     */
    public function testSuffix()
    {
        $query = $this->getQuery()
            ->select('*')
            ->from('foo')
            ->where('a = b')
            ->order('id')
            ->suffix('FOR UPDATE');

        $sql = 'SELECT * FROM foo WHERE a = b ORDER BY id FOR UPDATE';

        $this->assertEquals($this->format($sql), $this->format($query));
    }

    /**
     * testForUpdate
     *
     * @return  void
     */
    public function testForUpdate()
    {
        $query = $this->getQuery()
            ->select('*')
            ->from('foo')
            ->where('a = b')
            ->order('id')
            ->forUpdate();

        $sql = 'SELECT * FROM foo WHERE a = b ORDER BY id FOR UPDATE';

        $this->assertEquals($this->format($sql), $this->format($query));
    }

    /**
     * testAlias
     *
     * @return  void
     *
     * @since  3.4.9
     */
    public function testQueryAlias()
    {
        $query = $this->getQuery()
            ->select('*')
            ->from('foo')
            ->where('a = b')
            ->order('id')
            ->alias('foo');

        $sql = '(SELECT * FROM foo WHERE a = b ORDER BY id) AS foo';

        self::assertEquals($this->format($sql), $this->format($query));
    }
}
