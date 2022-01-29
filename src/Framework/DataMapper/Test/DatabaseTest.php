<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\DataMapper\Test;

use Windwalker\Legacy\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Legacy\DataMapper\Adapter\AbstractDatabaseAdapter;
use Windwalker\Legacy\DataMapper\Adapter\WindwalkerAdapter;

/**
 * The DatabaseTest class.
 *
 * @since  2.0
 */
abstract class DatabaseTest extends AbstractDatabaseTestCase
{
    /**
     * Property driver.
     *
     * @var  string
     */
    protected static $driver = 'mysql';

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    /**
     * loadToDataset
     *
     * @param mixed  $query
     * @param string $class
     * @param string $dataClass
     *
     * @return  mixed
     */
    protected function loadToDataset(
        $query,
        $class = 'Windwalker\Legacy\\Data\\DataSet',
        $dataClass = 'Windwalker\Legacy\\Data\\Data'
    ) {
        $dataset = $this->db->setQuery($query)->loadAll(null, $dataClass);

        return new $class($dataset);
    }

    /**
     * loadToData
     *
     * @param mixed  $query
     * @param string $dataClass
     *
     * @return  mixed
     */
    protected function loadToData($query, $dataClass = 'Windwalker\Legacy\\Data\\Data')
    {
        $data = $this->db->setQuery($query)->loadOne($dataClass);

        return $data;
    }

    /**
     * show
     *
     * @return  void
     */
    protected function show()
    {
        foreach (func_get_args() as $key => $arg) {
            echo sprintf("\n[Value %d]\n", $key + 1);
            print_r($arg);
        }
    }
}
