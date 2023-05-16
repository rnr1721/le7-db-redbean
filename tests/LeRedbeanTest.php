<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

use RedBeanPHP\R;
use Core\Database\Redbean\Interfaces\EntificationSqlInterface;
use Core\Database\Redbean\Db;
use Core\Database\Redbean\DbConn;
use Core\Database\Redbean\Drivers\DbSql;
use Core\Database\Redbean\Drivers\DbSqlite;
use Core\Database\Redbean\EntificationSql;
use Core\Database\Redbean\Interfaces\DbInterface;
use Core\Database\Redbean\Interfaces\DbConnInterface;
use Core\Entify\Entification;
use Core\Entify\Interfaces\EntificationInterface;
use Core\Entify\Interfaces\RulesLoaderInterface;
use Core\Entify\RulesLoaderClass;
use PHPUnit\Framework\TestCase;

class LeRedbeanTest extends TestCase
{

    public string $dbFile = './tests/db.sqlite';

    protected function setUp(): void
    {
        $this->deleteDatabase();
    }

    public function testSqliteConfig()
    {
        $connectionArray = [
            'driver' => 'sqlite',
            'path' => $this->dbFile
        ];

        $driver = new DbSqlite();
        $driver->setDbPath($this->dbFile);
        $this->assertEquals('sqlite:' . $this->dbFile, $driver->getString());
        $this->assertEquals($connectionArray, $driver->export());
    }

    public function testSqlConfig()
    {
        $connectionArray = [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => '7777',
            'user' => 'user',
            'name' => 'database',
            'pass' => '123'
        ];
        $driver = new DbSql();
        $driver->setDbHost('127.0.0.1')
                ->setDbName('database')
                ->setDbPassword('123')
                ->setDbPort('7777')
                ->setDbUser('user')
                ->setDriver('pgsql');
        $this->assertEquals('pgsql:host=127.0.0.1;dbname=database;port=7777', $driver->getString());
        $this->assertEquals($connectionArray, $driver->export());
        $this->assertEquals('pgsql', $driver->getName());
    }

    public function testDbConnection(): void
    {

        $db = $this->getDb();
        $this->assertFalse($db->getConnection()->isConnected());

        R::testConnection();

        $this->assertTrue($db->getConnection()->isConnected());

        $db->getConnection()->disconnect();
        $this->assertFalse($db->getConnection()->isConnected());

        $this->deleteDatabase();
    }

    public function testSingle()
    {
        $db = $this->getDb();
        $bean = $db->dispense('contact');
        $bean->name = 'John';
        $bean->lastname = 'Doe';
        $bean->another = '';

        try {
            $db->store($bean);
        } catch (Exception $ex) {
            $this->assertEquals(1, count($bean->getErrors()));
        }

        $bean->another = '777';
        $db->store($bean);

        $new = $db->findOne('contact', ' id = ? ', [1]);

        $this->assertEquals('John', $new->name);
        $this->assertEquals('Doe', $new->lastname);
        $this->assertEquals('777', $new->another);

        $db->trash($new);

        $notexists = $db->findOne('contact', ' id = ? ', [1]);

        $this->assertNull($notexists);

        $db->getConnection()->disconnect();
        $this->assertFalse($db->getConnection()->isConnected());
        $this->deleteDatabase();
    }

    public function testMultiple()
    {
        $entificationSql = $this->getPreparedEntificationSql();
        $provider = $entificationSql->getDataProvider('contact');
        $page1 = $provider->paginate(5, 1)->getEntity();
        $page1data = $page1->export();
        $this->assertEquals(5, count($page1data));
        $page2 = $provider->paginate(5, 2)->getEntity();
        $page2data = $page2->export();
        $this->assertEquals(5, count($page2data));
        $special = $provider->select()->from()->where('id="2"')->getEntity()->exportOne();
        $this->assertEquals('2', $special['id']);
        $this->assertEquals('John2', $special['name']);
        $all = $provider->getEntity()->export();
        $this->assertEquals(10, count($all));
        $this->deleteDatabase();
    }

    public function getDb(): DbInterface
    {
        return new Db($this->getConnection());
    }

    public function getConnection(): DbConnInterface
    {
        return new DbConn($this->getDriverSqlite(), $this->getEntification());
    }

    public function getPreparedEntificationSql(): EntificationSqlInterface
    {
        $db = $this->getDb();
        for ($i = 1; $i <= 10; $i++) {
            $bean = $db->dispense('contact');
            $bean->name = 'John' . $i;
            $bean->lastname = 'Doe' . $i;
            $bean->another = 'somedata' . $i;
            $db->store($bean);
        }
        $db->getConnection()->disconnect();
        return $this->getEntificationSql();
    }

    public function getEntificationSql(): EntificationSqlInterface
    {
        return new EntificationSql($this->getLoader(), $this->getDb());
    }

    public function getEntification(): EntificationInterface
    {
        return new Entification($this->getLoader());
    }

    public function getDriverSqlite(): DbSqlite
    {
        return new DbSqlite([
            'path' => $this->dbFile,
            'namespace' => '\\TestsModel\\'
        ]);
    }

    public function getLoader(): RulesLoaderInterface
    {
        return new RulesLoaderClass('\\TestsModel\\');
    }

    public function deleteDatabase(): void
    {
        if (file_exists($this->dbFile)) {
            R::close();
            unlink($this->dbFile);
        }
    }

}
