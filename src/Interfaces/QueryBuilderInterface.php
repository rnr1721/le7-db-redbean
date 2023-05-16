<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

interface QueryBuilderInterface
{

    /**
     * SELECT * for query
     * @param string $columns Example: "id, name, lastname"
     * @return self
     */
    public function select(string $columns = '*'): self;

    /**
     * FROM {table}
     * @param string $table Example: "users"
     * @return self
     */
    public function from(string $table = ''): self;

    /**
     * JOIN for table
     * @param string $table Table name
     * @param string $condition Example: "t1.id = t2.id"
     * @return self
     */
    public function join(string $table, string $condition): self;

    /**
     * WHERE {conditions}
     * @param string $condition Example: "a = ?"
     * @return self
     */
    public function where(string $condition): self;

    /**
     * Order by some columns
     * @param string $columns Example: "name ASC"
     * @return self
     */
    public function orderBy(string $columns): self;

    /**
     * Group by some
     * @param string $columns Example: "users.name"
     * @return self
     */
    public function groupBy(string $columns): self;

    /**
     * HAVING {condition}
     * @param string $condition Example: "total_sum > 1000"
     * @return self
     */
    public function having(string $condition): self;

    /**
     * Subquery
     * @param string $query Example: "SELECT * FROM users"
     * @param string|null $alias Example: "sub"
     * @return self
     */
    public function subquery(string $query, ?string $alias = null): self;

    /**
     * Set glue
     * @param string $glue Example: "OR" or "AND"
     * @return self
     */
    public function setGlue(string $glue): self;

    /**
     * Reset Query builder condition
     * @return self
     */
    public function resetQuery(): self;

    /**
     * Build SQL query
     * @return string
     */
    public function buildQuery(): string;
}
