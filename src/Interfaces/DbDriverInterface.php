<?php

namespace Core\Database\Redbean\Interfaces;

/**
 * Database configuration object.
 * it make config as string to make RedbeanPHP connection string
 */
interface DbDriverInterface
{

    /**
     * Get is DB frozen from driver config
     * @return bool
     */
    public function getFrozen(): bool;

    /**
     * If model class not exists, throw RuntimeException
     * @return bool
     */
    public function getErrorIfModelNotExists(): bool;

    /**
     * Get namespace for models
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Get driver name
     * @return string
     */
    public function getName(): string;

    /**
     * Export configuration as array
     * @return array
     */
    public function export(): array;

    /**
     * Get connection string
     * @return string
     */
    public function getString(): string;
}
