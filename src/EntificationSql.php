<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use Core\Database\Redbean\Interfaces\DbInterface;
use Core\Entify\Interfaces\RulesLoaderInterface;
use Core\Entify\HandlerFactory;
use Core\Database\Redbean\Interfaces\EntificationSqlInterface;
use Core\Database\Redbean\Interfaces\DataProviderSqlInterface;

/**
 * Main factory for build SQL providers
 */
class EntificationSql implements EntificationSqlInterface
{

    /**
     * Rules loader of model
     * @var RulesLoaderInterface
     */
    protected RulesLoaderInterface $loader;

    /**
     * Redbean wrapper
     * @var DbInterface
     */
    protected DbInterface $db;

    public function __construct(RulesLoaderInterface $loader, DbInterface $db)
    {
        $this->loader = $loader;
        $this->db = $db;
    }

    /**
     * @inheritdoc
     */
    public function getDataProvider(
            string $modelName,
            array $bindings = [],
            string $query = null
    ): DataProviderSqlInterface
    {
        $factory = new HandlerFactory($this->loader->getRules($modelName));
        $handlers = $factory->getHandlers();
        return new SqlDataProvider($handlers, $this->db, $modelName, $query, $bindings);
    }

}
