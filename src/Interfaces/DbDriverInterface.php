<?php

namespace Core\Database\Redbean\Interfaces;

interface DbDriverInterface
{

    public function getFrozen(): bool;

    public function getErrorIfModelNotExists(): bool;

    public function getNamespace(): string;

    public function getName(): string;

    public function export(): array;

    public function getString(): string;
}
