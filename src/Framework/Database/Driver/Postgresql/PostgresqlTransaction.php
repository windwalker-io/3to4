<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Database\Driver\Postgresql;

use Windwalker\Legacy\Database\Driver\Pdo\PdoTransaction;

/**
 * Class PostgresqlTransaction
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class PostgresqlTransaction extends PdoTransaction
{
    /**
     * start
     *
     * @return  static
     */
    public function start()
    {
        if (!$this->nested || !$this->depth) {
            parent::start();
        } else {
            $savepoint = 'SP_' . $this->depth;
            $this->db->setQuery('SAVEPOINT ' . $this->db->quoteName($savepoint));

            if ($this->db->execute()) {
                $this->depth++;
            }
        }

        return $this;
    }

    /**
     * rollback
     *
     * @return  static
     */
    public function rollback()
    {
        if (!$this->nested || $this->depth <= 1) {
            parent::rollback();
        } else {
            $savepoint = 'SP_' . ($this->depth - 1);
            $this->db->setQuery('ROLLBACK TO SAVEPOINT ' . $this->db->quoteName($savepoint));

            if ($this->db->execute()) {
                $this->depth--;
            }
        }

        return $this;
    }
}
