<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use \RuntimeException;

/**
 * Abstract class. Any drivers must extend of it
 */
abstract class RedbeanDriverAbstract
{

    /**
     * If model not exists, it will drop RuntimeException
     * @var bool
     */
    protected bool $errorIfModelNotExists = true;

    /**
     * Namespace for find models
     * @var string
     */
    protected string $namespace = '\\Model\\';
    protected bool $frozen = false;

    /**
     * Driver to connect db
     * @var string|null
     */
    protected ?string $driver = null;

    public function __construct(array $params = [])
    {
        foreach ($params as $paramKey => $paramValue) {
            if (property_exists($this, $paramKey) && is_string($paramValue)) {
                $this->{$paramKey} = $paramValue;
            } else {
                throw new RuntimeException("DB parameter not exists: " . $paramKey);
            }
        }
    }

    /**
     * RuntimeException if model not exists
     * @param bool $value
     * @return self
     */
    public function setErrorIfModelNotExists(bool $value): self
    {
        $this->errorIfModelNotExists = $value;
        return $this;
    }

    /**
     * Set frozen or unfrozen for Db
     * @param bool $value True or false
     * @return self
     */
    public function setFrozen(bool $value): self
    {
        $this->frozen = $value;
        return $this;
    }

    /**
     * Get if frozen or unfrozen
     * @return bool
     */
    public function getFrozen(): bool
    {
        return $this->frozen;
    }

    /**
     * Runtime exception if model not exists
     * @return bool
     */
    public function getErrorIfModelNotExists(): bool
    {
        return $this->errorIfModelNotExists;
    }

    /**
     * Set namespace for models
     * @param string $namespace
     * @return self
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get namespace for models
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Get driver name
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->driver;
    }

    /**
     * Export driver data
     */
    abstract public function export(): array;

    /**
     * Get connection string
     */
    abstract public function getString(): string;
}
