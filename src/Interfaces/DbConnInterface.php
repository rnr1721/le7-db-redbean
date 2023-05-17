<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

use Core\Database\Redbean\Interfaces\DbDriverInterface;

/**
 * Database connection object. It is wrapper of RedbeanPHP connection
 * methods. It can switch database, connect, disconnect or indicate
 * connection status. This object used by DbInterface as dependency
 */
interface DbConnInterface
{

    /**
     * Switch to database and make it active
     * @param DbDriverInterface $driver Instance of DbSql ot SbSqlite
     * @param string $key Db key
     * @param bool|null $frozen Frozen (see Redbean documentation)
     * @return void
     */
    public function switchDatabase(
            DbDriverInterface $driver,
            string $key = 'default',
            ?bool $frozen = null
    ): void;

    /**
     * Disconnect database
     * @return void
     */
    public function disconnect(): void;

    /**
     * If connection established
     * @return bool
     */
    public function isConnected(): bool;
}
