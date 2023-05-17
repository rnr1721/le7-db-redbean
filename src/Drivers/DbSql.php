<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Drivers;

use Core\Database\Redbean\Interfaces\DbDriverInterface;
use Core\Database\Redbean\RedbeanDriverAbstract;
use \RuntimeException;

class DbSql extends RedbeanDriverAbstract implements DbDriverInterface
{

    protected ?string $port = null;
    protected string $host = 'localhost';
    protected ?string $name = null;
    protected ?string $user = null;
    protected ?string $password = null;

    /**
     * Set database host
     * @param string $host Default localhost
     * @return self
     */
    public function setDbHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Set database name
     * @param string $name Database name
     * @return self
     */
    public function setDbName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set database username
     * @param string $user Database user
     * @return self
     */
    public function setDbUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set database password
     * @param string $password
     * @return self
     */
    public function setDbPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set database port
     * @param string $port Port number
     * @return self
     */
    public function setDbPort(string $port): self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Select driver: pgsql or mysql or curbid
     * Mysql is for both mysql and mariadb
     * @param string $driver pgsql or sqlite or curbid
     * @return self
     * @throws RuntimeException
     */
    public function setDriver(string $driver): self
    {
        $allow = ['pgsql', 'mysql', 'curbid'];
        if (!in_array($driver, $allow)) {
            $allowed = implode(', ', $allow);
            throw new RuntimeException("Allowed drivers: " . $allowed);
        }
        $this->driver = $driver;
        return $this;
    }

    public function export(): array
    {
        return [
            'driver' => $this->getName(),
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'name' => $this->name,
            'pass' => $this->password
        ];
    }

    public function getString(): string
    {

        if ($this->user === null) {
            throw new RuntimeException("DbSql::connect Please set DB user");
        }

        if ($this->password === null) {
            throw new RuntimeException("DbSql::connect Please set DB pass");
        }

        if ($this->driver === null) {
            throw new RuntimeException("DbSql::connect Please set driver");
        }

        if ($this->name === null) {
            throw new RuntimeException("DbSql::connect Please set DB name");
        }

        $drv = $this->driver;
        $host = $this->host;
        $name = $this->name;

        $port = ';port=' . $this->port ?? '';

        return "$drv:host=$host;dbname=$name" . $port;
    }

}
