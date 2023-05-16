<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

use Core\Database\Redbean\RedbeanDriverAbstract;

interface DbConnInterface
{

    /**
     * Switch to database and make it active
     * @param RedbeanDriverAbstract $driver Instance of DbSql ot SbSqlite
     * @param string $key Db key
     * @param bool|null $frozen Frozen (see Redbean documentation)
     * @return void
     */
    public function switchDatabase(
            RedbeanDriverAbstract $driver,
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
