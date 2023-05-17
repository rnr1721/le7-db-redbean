<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Drivers;

use Core\Database\Redbean\Interfaces\DbDriverInterface;
use Core\Database\Redbean\RedbeanDriverAbstract;
use \RuntimeException;

class DbSqlite extends RedbeanDriverAbstract implements DbDriverInterface
{

    protected ?string $path = null;

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->driver = 'sqlite';
    }

    /**
     * Set database path
     * @param string $path
     * @return self
     */
    public function setDbPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function export(): array
    {
        return [
            'driver' => $this->getName(),
            'path' => $this->path
        ];
    }

    public function getString(): string
    {
        if ($this->path === null) {
            throw new RuntimeException("DbSql::connect Please set DB path");
        }

        return $this->driver . ':' . $this->path;
    }

}
