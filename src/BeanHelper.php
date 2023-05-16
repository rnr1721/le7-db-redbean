<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use RedBeanPHP\OODBBean;
use RedBeanPHP\BeanHelper\SimpleFacadeBeanHelper;
use \RuntimeException;

/**
 * Modified BeanHelper
 */
class BeanHelper extends SimpleFacadeBeanHelper
{

    /**
     * Namespace for models
     * @var string
     */
    private string $namespace;
    
    /**
     * If true, if model class not exists, it will throw RuntimeException
     * @var bool
     */
    private bool $errorIfNotExists = true;

    public function __construct(string $namespace, bool $errorIfNotExists = true)
    {
        $this->namespace = $namespace;
        $this->errorIfNotExists = $errorIfNotExists;
    }

    public function getModelForBean(OODBBean $bean)
    {
        $model = $bean->getMeta('type');
        $prefix = $this->namespace;
        /**
         * @psalm-suppress UndefinedDocblockClass
         */
        $result = $this->resolveModel($prefix, $model, $bean);
        if ($this->errorIfNotExists && !$result) {
            throw new RuntimeException('Model not exists:' . ' ' . $model);
        }
        /**
         * @psalm-suppress InvalidReturnStatement
         */
        return $result;
    }

}
