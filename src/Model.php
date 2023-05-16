<?php

declare(strict_types=1);

namespace Core\Database\Redbean;

use RedBeanPHP\R;
use Core\Entify\Interfaces\EntificationInterface;
use RedBeanPHP\TypedModel;
use \RuntimeException;
use \InvalidArgumentException;
use function basename,
             str_replace,
             get_called_class,
             method_exists,
             array_merge,
             count;

/**
 * Generic model. From this model must extends all models
 */
class Model extends TypedModel
{

    /**
     * Entification object for creating array data provider
     * @var EntificationInterface|null
     */
    protected ?EntificationInterface $entification = null;

    /**
     * Errors array
     * @var array
     */
    protected array $errors = [];

    /**
     * Get current model name
     * @return string
     */
    public function getModelName(): string
    {
        return basename(str_replace('\\', '/', get_called_class()));
    }

    /**
     * Validating and normalizing entity
     * @return void
     * @throws RuntimeException
     */
    public function update(): void
    {

        // Validation and normalize
        if (!method_exists($this, 'getRules')) {
            throw new RuntimeException("method getRules() not found in entity");
        }
        $data = $this->bean->export();
        /** @var array $rules */
        $rules = $this->getRules();
        if (!$this->entification instanceof EntificationInterface) {
            throw new RuntimeException("Entification framework not found");
        }
        $provider = $this->entification->getArrayProvider($data, $rules);

        // We dont need hide records here
        $provider->getOptions()->setAllowHideFilter(false);

        $entity = $provider->getEntity();
        $result = $entity->exportOne();

        $errors = $entity->getErrors();
        if ($errors) {
            $this->errors = array_merge($this->errors, $errors);
            throw new InvalidArgumentException("Errors while save record" . ' ' . $this->bean);
        }

        // Check for unique fields
        foreach ($rules as $field => $options) {
            if (isset($options['unique']) && $options['unique'] === true) {
                /** @var array $result */
                $conds = [$result[$field]];
                $query = " WHERE $field = ? ";
                $potentialDuplicate = R::findOne($this->getModelName(), $query, $conds);
                if ($potentialDuplicate) {
                    $this->errors[] = _("Field") . ' ' . $options['label'] . ' ' . _("must be unique");
                }
            }
        }

        if ($this->getErrors() || !is_array($result)) {
            throw new InvalidArgumentException("Errors while save record" . ' ' . $this->bean);
        }

        $this->bean->import($result);
    }

    /**
     * After update we clean errors array
     * @return void
     */
    public function after_update(): void
    {
        $this->errors = [];
    }

    /**
     * Set Entification factory to get Array data provider
     * @param EntificationInterface $entification
     * @return void
     */
    public function setEntification(EntificationInterface $entification): void
    {
        $this->entification = $entification;
    }

    /**
     * Get errors array if errors
     * @return array|null
     */
    public function getErrors(): array|null
    {
        if (count($this->errors) === 0) {
            return null;
        }
        $errors = $this->errors;
        $this->errors = [];
        return $errors;
    }

}
