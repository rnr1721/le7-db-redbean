<?php

declare(strict_types=1);

namespace TestsModel;

use Core\Entify\Interfaces\ModelInterface;
use Core\Database\Redbean\Model;

class Contact extends Model implements ModelInterface
{
    
    public function getRules(): array
    {
        return [
            'id' => [
                'label' => 'ID',
                'validate' => ''
            ],
            'name' => [
                'label' => 'Name',
                'validate' => ''
            ],
            'lastname' => [
                'label' => 'Lastname',
                'validate' => ''
            ],
            'another' => [
                'label' => 'Another field',
                'validate' => 'required',
                'unique' => true
            ]
        ];
    }

}
