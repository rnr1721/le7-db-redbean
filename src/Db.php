<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use RedBeanPHP\RedException\SQL;
use Core\Database\Redbean\Interfaces\DbInterface;
use Core\Database\Redbean\Interfaces\DbConnInterface;
use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

/**
 * Redbean wrapper for methods that working with database
 * It wrap static methods
 */
class Db implements DbInterface
{

    protected DbConnInterface $dbConn;

    public function __construct(DbConn $dbConn)
    {
        $this->dbConn = $dbConn;
    }

    /**
     * @inheritdoc
     */
    public function getConnection(): DbConnInterface
    {
        return $this->dbConn;
    }

    /**
     * @inheritdoc
     */
    public function load(string $type, int $id, string $snippet = null): OODBBean
    {
        return R::load($type, $id, $snippet);
    }

    /**
     * @inheritdoc
     */
    public function getAll(string $sql, array $bindings = array()): array
    {
        return R::getAll($sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function getCell(string $sql, array $bindings = array()): string|null
    {
        return R::getCell($sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function getRow(string $sql, array $bindings = array()): array|null
    {
        return R::getRow($sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function findOne(string $type, string $sql = null, array $bindings = array()): OODBBean|null
    {
        return R::findOne($type, $sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function findAll(string $type, string $sql = null, array $bindings = array()): array
    {
        return R::findAll($type, $sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function dispense(string|array $typeOrBeanArray, int $num = 1, bool $alwaysReturnArray = FALSE): array|OODBBean
    {
        return R::dispense($typeOrBeanArray, $num, $alwaysReturnArray);
    }

    /**
     * @inheritdoc
     * @throws SQL
     */
    public function store(OODBBean|SimpleModel $bean, bool $unfreezeIfNeeded = FALSE): int|string
    {
        return R::store($bean, $unfreezeIfNeeded);
    }

    /**
     * @inheritdoc
     */
    public function trash(string|OODBBean|SimpleModel $beanOrType, int $id = null): int
    {
        return R::trash($beanOrType, $id);
    }

    /**
     * @inheritdoc
     */
    public function trashAll(array $beans): int
    {
        return R::trashAll($beans);
    }

    /**
     * @inheritdoc
     */
    public function inspect(string|null $type = null): array
    {
        return R::inspect($type);
    }

    /**
     * @inheritdoc
     */
    public function batch(string $type, array $ids): array
    {
        return R::batch($type, $ids);
    }

    /**
     * @inheritdoc
     */
    public function storeAll(array $beans, bool $unfreezeIfNeeded = FALSE): array
    {
        return R::storeAll($beans, $unfreezeIfNeeded);
    }

    /**
     * @inheritdoc
     */
    public function count(string $type, string $addSQL = '', array $bindings = array()): int
    {
        return R::count($type, $addSQL, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function exec(string $sql, array $bindings = array()): int
    {
        return R::exec($sql, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function genSlots(array $array, string $template = null): string
    {
        return R::genSlots($array, $template);
    }

}
