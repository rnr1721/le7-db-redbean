<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

/**
 * Query builder trait to use in own classes
 */
trait QueryBuilderTrait
{

    /**
     * Predefined table,
     * to use from() method without parameter if need
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * Pieces
     * @var array
     */
    private array $pieces = [];

    /**
     * Current glue. Must be defined in class constructor
     * @var string|null
     */
    private ?string $glue = null;

    /**
     * @inheritdoc
     */
    public function select(string $columns = '*'): self
    {
        $this->pieces[] = ['SELECT', $columns];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function from(string $table = ''): self
    {
        if (is_string($this->table)) {
            $table = $this->table;
        }
        $this->pieces[] = ['FROM', $table];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function join(string $table, string $condition): self
    {
        $this->pieces[] = ['JOIN', $table, $condition];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function where(string $condition): self
    {
        $this->pieces[] = ['WHERE', $condition];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orderBy(string $columns): self
    {
        $this->pieces[] = ['ORDER BY', $columns];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function groupBy(string $columns): self
    {
        $this->pieces[] = ['GROUP BY', $columns];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function having(string $condition): self
    {
        $this->pieces[] = ['HAVING', $condition];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function subquery(string $query, ?string $alias = null): self
    {
        $subquery = '(' . $query . ')' . ($alias ? " AS $alias" : '');
        $this->pieces[] = ['SUBQUERY', $subquery];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setGlue(string $glue): self
    {
        $this->glue = $glue;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetQuery(): self
    {
        $this->glue = null;
        $this->pieces = [];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function buildQuery(): string
    {
        $sql = '';
        $glue = $this->glue;

        foreach ($this->pieces as $piece) {
            $n = count($piece);

            switch ($n) {
                case 1:
                    $sql .= " {$piece[0]} ";
                    break;
                case 2:
                    $glue = null;
                    if (!is_null($piece[0])) {
                        $sql .= "{$piece[0]} {$piece[1]} ";
                    }
                    break;
                case 3:
                    $glue = (is_null($glue)) ? $piece[1] : $glue;
                    if (!is_null($piece[0])) {
                        $sql .= "{$glue} {$piece[2]} ";
                        $glue = null;
                    }
                    break;
            }
        }

        return trim($sql);
    }

}
