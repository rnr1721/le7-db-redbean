<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use Core\Database\Redbean\Interfaces\DbInterface;
use Core\Database\Redbean\Interfaces\DataProviderSqlInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\Interfaces\EntityInterface;
use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\EntityMain;
use Core\Entify\Paginator;

/**
 * Data Provider for Redbean SQL
 */
class SqlDataProvider implements DataProviderSqlInterface
{

    use QueryBuilderTrait;

    /**
     * Entity Handlers collection object for Entification
     * @var EntityHandlersInterface
     */
    protected EntityHandlersInterface $entityHandlers;

    /**
     * Database wrapper object
     * @var DbInterface
     */
    protected DbInterface $db;

    /**
     * SQL Query
     * @var string|null
     */
    protected ?string $query = null;

    /**
     * Bindings for Redbean and PDO
     * @var array
     */
    protected array $bindings = [];

    /**
     * Pagination options if you will paginate
     * @var array|null
     */
    protected ?array $pagination = null;

    public function __construct(
            EntityHandlersInterface $entityHandlers,
            DbInterface $db,
            string $table,
            string|null $query = null,
            array $bindings = [],
    )
    {
        $this->entityHandlers = $entityHandlers;
        $this->db = $db;
        $this->query = $query;
        $this->bindings = $bindings;
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function paginate(
            int $perPage = 15,
            int $page = 1,
            int $prevCount = 5,
            int $nextCount = 5
    ): DataProviderSqlInterface
    {
        $this->pagination = [
            'page' => $page,
            'per_page' => $perPage,
            'prev_count' => $prevCount,
            'next_count' => $nextCount
        ];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEntity(): EntityInterface
    {

        $query = $this->query ?? $this->buildQuery();

        if (empty($query)) {
            $query = "SELECT * FROM $this->table";
        }

        $p = $this->pagination;

        if ($p !== null) {
            $totalCountResult = $this->db->getAll('SELECT COUNT(*) FROM (' . $query . ') AS count_query', $this->bindings);
            if (isset($totalCountResult[0]['COUNT(*)'])) {
                $totalCount = intval($totalCountResult[0]['COUNT(*)']);
            } else {
                $totalCount = 0;
            }

            $paginator = new Paginator(
                    $p['page'],
                    $p['per_page'],
                    $totalCount
            );

            $offset = $paginator->getOffset();

            $query = $query . " LIMIT $offset, " . $p['per_page'];
            $result = $this->db->getAll($query, $this->bindings);

            $info = [
                'pagination' => $paginator->toArray(count($result), $p['prev_count'], $p['next_count'])
            ];
        } else {
            $info = [
                'pagination' => []
            ];
            $result = $this->db->getAll($query, $this->bindings);
        }

        $this->restoreState();

        return new EntityMain(
                $this->entityHandlers,
                $result,
                $info
        );
    }

    /**
     * Restore clean state of this class
     * @return void
     */
    private function restoreState(): void
    {
        $this->pagination = null;
        $this->query = null;
        $this->bindings = [];
        $this->resetQuery();
    }

    public function getOptions(): EntityOptionsInterface
    {
        return $this->entityHandlers->getOptions();
    }

}
