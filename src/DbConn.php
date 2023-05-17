<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use Core\Database\Redbean\Interfaces\DbDriverInterface;
use Core\Database\Redbean\Interfaces\DbConnInterface;
use Core\Entify\Interfaces\EntificationInterface;
use RedBeanPHP\R;

/**
 * DB connection class
 * It can connect, switch between DB, disconnect or check connection status
 */
class DbConn implements DbConnInterface
{

    /**
     * Entification object for validate and normalize
     * @var EntificationInterface
     */
    protected EntificationInterface $entification;

    public function __construct(
            DbDriverInterface $driver,
            EntificationInterface $entification
    )
    {
        $this->entification = $entification;
        $this->switchDatabase($driver, 'default');
    }

    /**
     * @inheritdoc
     */
    public function switchDatabase(
            DbDriverInterface $driver,
            string $key = 'default',
            ?bool $frozen = null
    ): void
    {

        if (R::getToolBoxByKey($key)) {
            R::selectDatabase($key);
            return;
        }

        if ($key === 'default') {
            $entification = $this->entification;
            $beanHelper = new BeanHelper(
                    $driver->getNamespace(),
                    $driver->getErrorIfModelNotExists()
            );
            $beanHelper->setFactoryFunction(
                    function (string $name) use ($entification) {
                        /** @var class-string $name */
                        $model = new $name();
                        $model->setEntification($entification);
                        return $model;
                    }
            );
        }

        if ($frozen === null) {
            $frozen = $driver->getFrozen();
        }

        $options = $driver->export();
        $user = $options['user'] ?? null;
        $pass = $options['pass'] ?? null;
        R::addDatabase(
                $key,
                $driver->getString(),
                $user,
                $pass,
                $frozen,
                false,
                [],
                $beanHelper ?? null
        );
        R::selectDatabase($key);
    }

    /**
     * @inheritdoc
     */
    public function disconnect(): void
    {
        R::close();
    }

    /**
     * @inheritdoc
     */
    public function isConnected(): bool
    {
        /**
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return R::getDatabaseAdapter()->getDatabase()->isConnected();
    }

}
