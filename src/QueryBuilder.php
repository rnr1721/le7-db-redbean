<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use Core\Database\Redbean\Interfaces\QueryBuilderInterface;

/**
 * Query Builder class to build SQL queries
 */
class QueryBuilder implements QueryBuilderInterface
{

    use QueryBuilderTrait;

    public function __construct(?string $table = null, ?string $glue = null)
    {
        if ($table) {
            $this->table = $table;
        }
        if ($glue) {
            $this->glue = $glue;
        }
    }

}
