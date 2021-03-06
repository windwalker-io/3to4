<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Legacy\DataMapper\Test\Stub;

use Windwalker\Legacy\Data\DataSet;
use Windwalker\Legacy\DataMapper\AbstractDataMapper;
use Windwalker\Legacy\Event\DispatcherAwareInterface;

/**
 * The StubDispatcherAwareDatamapper class.
 *
 * @since  2.1
 * @deprecated Legacy code
 */
class StubDispatcherAwareDatamapper extends AbstractDataMapper implements DispatcherAwareInterface
{
    /**
     * Property args.
     *
     * @var  array
     */
    public $args = [];

    /**
     * Property select.
     *
     * @var  array
     */
    public $select = [];

    /**
     * Do find action, this method should be override by sub class.
     *
     * @param array   $conditions Where conditions, you can use array or Compare object.
     * @param array   $orders     Order sort, can ba string, array or object.
     * @param integer $start      Limit start number.
     * @param integer $limit      Limit rows.
     *
     * @param         $key
     *
     * @return  mixed Found rows data set.
     */
    protected function doFind(array $conditions, array $orders, $start, $limit, $key)
    {
        $this->args = func_get_args();

        return new DataSet([['method' => __FUNCTION__]]);
    }

    /**
     * Do create action, this method should be override by sub class.
     *
     * @param mixed $dataset The data set contains data we want to store.
     *
     * @return  mixed  Data set data with inserted id.
     */
    protected function doCreate($dataset)
    {
        return new DataSet([['method' => __FUNCTION__]]);
    }

    /**
     * Do update action, this method should be override by sub class.
     *
     * @param mixed $dataset      Data set contain data we want to update.
     * @param array $condFields   The where condition tell us record exists or not, if not set,
     *                            will use primary key instead.
     * @param bool  $updateNulls  Update empty fields or not.
     *
     * @return  mixed Updated data set.
     */
    protected function doUpdate($dataset, array $condFields, $updateNulls = false)
    {
        return new DataSet([['method' => __FUNCTION__]]);
    }

    /**
     * Do updateAll action, this method should be override by sub class.
     *
     * @param mixed $data       The data we want to update to every rows.
     * @param mixed $conditions Where conditions, you can use array or Compare object.
     *
     * @return  boolean
     */
    protected function doUpdateBatch($data, array $conditions)
    {
        return true;
    }

    /**
     * Do flush action, this method should be override by sub class.
     *
     * @param mixed $dataset    Data set contain data we want to update.
     * @param mixed $conditions Where conditions, you can use array or Compare object.
     *
     * @return  mixed Updated data set.
     */
    protected function doFlush($dataset, array $conditions)
    {
        return new DataSet([['method' => __FUNCTION__]]);
    }

    /**
     * Do delete action, this method should be override by sub class.
     *
     * @param mixed $conditions Where conditions, you can use array or Compare object.
     *
     * @return  boolean Will be always true.
     */
    protected function doDelete(array $conditions)
    {
        return true;
    }

    /**
     * Get table fields.
     *
     * @param string $table Table name.
     *
     * @return  array
     */
    public function getFields($table = null)
    {
        return [];
    }

    /**
     * select
     *
     * @param   string|array $column
     *
     * @return  static
     */
    public function select($column)
    {
        array_merge($this->select, (array) $column);

        return $this;
    }

    /**
     * doFindIterate
     *
     * @param array   $conditions Where conditions, you can use array or Compare object.
     * @param array   $orders     Order sort, can ba string, array or object.
     * @param integer $start      Limit start number.
     * @param integer $limit      Limit rows.
     *
     * @return  \Iterator
     *
     * @since  3.5.19
     */
    protected function doFindIterate(array $conditions, $order, ?int $start, ?int $limit): \Iterator
    {
        return new \ArrayIterator();
    }

    /**
     * doSync
     *
     * @param  mixed       $dataset      Data set contain data we want to update.
     * @param  array       $conditions   Where conditions, you can use array or Compare object.
     * @param  array|null  $compareKeys  Thr compare keys to check update, keep or delete.
     *
     * @return  array
     *
     * @since  3.5.21
     */
    protected function doSync($dataset, array $conditions, ?array $compareKeys = null): array
    {
        return [];
    }
}
