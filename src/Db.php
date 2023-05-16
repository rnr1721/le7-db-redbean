<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use RedBeanPHP\RedException\SQL;
use Core\Database\Redbean\Interfaces\DbInterface;
use Core\Database\Redbean\Interfaces\DbConnInterface;
use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

class Db implements DbInterface
{

    protected DbConnInterface $dbConn;

    public function __construct(DbConn $dbConn)
    {
        $this->dbConn = $dbConn;
    }

    public function getConnection(): DbConnInterface
    {
        return $this->dbConn;
    }

    public function load(string $type, int $id, string $snippet = null): OODBBean
    {
        return R::load($type, $id, $snippet);
    }

    public function getAll(string $sql, array $bindings = array()): array
    {
        return R::getAll($sql, $bindings);
    }

    public function getCell(string $sql, array $bindings = array()): string|null
    {
        return R::getCell($sql, $bindings);
    }

    public function getRow(string $sql, array $bindings = array()): array|null
    {
        return R::getRow($sql, $bindings);
    }

    public function findOne(string $type, string $sql = null, array $bindings = array()): OODBBean|null
    {
        return R::findOne($type, $sql, $bindings);
    }

    public function findAll(string $type, string $sql = null, array $bindings = array()): array
    {
        return R::findAll($type, $sql, $bindings);
    }

    public function dispense(string|array $typeOrBeanArray, int $num = 1, bool $alwaysReturnArray = FALSE): array|OODBBean
    {
        return R::dispense($typeOrBeanArray, $num, $alwaysReturnArray);
    }

    /**
     * @throws SQL
     */
    public function store(OODBBean|SimpleModel $bean, bool $unfreezeIfNeeded = FALSE): int|string
    {
        return R::store($bean, $unfreezeIfNeeded);
    }

    public function trash(string|OODBBean|SimpleModel $beanOrType, int $id = null): void
    {
        R::trash($beanOrType, $id);
    }

    public function trashAll(array $beans): void
    {
        R::trashAll($beans);
    }

    public function inspect(string|null $type = null): array
    {
        return R::inspect($type);
    }

    public function batch(string $type, array $ids): array
    {
        return R::batch($type, $ids);
    }

    public function storeAll(array $beans, bool $unfreezeIfNeeded = FALSE): array
    {
        return R::storeAll($beans, $unfreezeIfNeeded);
    }

    public function count(string $type, string $addSQL = '', array $bindings = array()): int
    {
        return R::count($type, $addSQL, $bindings);
    }

    public function exec(string $sql, array $bindings = array()): int
    {
        return R::exec($sql, $bindings);
    }

    public function genSlots(array $array, string $template = null): string
    {
        return R::genSlots($array, $template);
    }

}
