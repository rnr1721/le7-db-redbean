<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

use Core\Database\Redbean\Interfaces\DbConnInterface;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

interface DbInterface
{

    public function getConnection(): DbConnInterface;

    public function load(string $type, int $id, string $snippet = null): OODBBean;

    public function getAll(string $sql, array $bindings = array()): array;

    public function getCell(string $sql, array $bindings = array()): string|null;

    public function getRow(string $sql, array $bindings = array()): array|null;

    public function findOne(string $type, string $sql = null, array $bindings = array()): OODBBean|null;

    public function findAll(string $type, string $sql = null, array $bindings = array()): array;

    public function dispense(string|array $typeOrBeanArray, int $num = 1, bool $alwaysReturnArray = FALSE): array|OODBBean;

    public function store(OODBBean|SimpleModel $bean, bool $unfreezeIfNeeded = FALSE): int|string;

    public function trash(string|OODBBean|SimpleModel $beanOrType, int $id = null): void;

    public function trashAll(array $beans): void;

    public function inspect(string|null $type = null): array;

    public function batch(string $type, array $ids): array;

    public function storeAll(array $beans, bool $unfreezeIfNeeded = FALSE): array;

    public function count(string $type, string $addSQL = '', array $bindings = array()): int;

    public function exec(string $sql, array $bindings = array()): int;

    public function genSlots(array $array, string $template = null): string;
}
