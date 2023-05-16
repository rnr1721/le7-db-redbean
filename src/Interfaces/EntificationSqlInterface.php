<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

interface EntificationSqlInterface
{

    /**
     * Get ready-to-use Redbean data provider
     * @param string $modelName Example: 'contact'
     * @param array $bindings Bindings for safe usage
     * @param string $query Custom query
     * @return DataProviderSqlInterface
     */
    public function getDataProvider(
            string $modelName,
            array $bindings = [],
            string $query = null
    ): DataProviderSqlInterface;
}
