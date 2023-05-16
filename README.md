# le7-db-redbean
Allow easy use pagination, validation and normalisation in any PHP project
that use Redbean PHP

## Requirements

- PHP 8.1 or higher.
- Composer 2.0 or higher

## Dependencies

All dependencies installs automatic by composer

- RedbeanPHP (https://github.com/gabordemooij/redbean)
- Entify Entity Framework (https://github.com/rnr1721/le7-entify)

## What it can?

- Easy-to-use models classes in some namespace
- Pagination using Entify entity framework
- Validation using rules (using Entify entity framework)
- use arrow RedbeanPHP method calls instead static methods

## Installation

```shell
composer require rnr1721/le7-db-redbean
```

## Testing

```shell
composer test
```

## How it works?

After installing, you need decide where will be your models.
For example in \Model. Any model is class, that contain entity rules.
IMPORTANT: validation rules must contain information about all entity fields.
Required fields is "label" and "validate". Any other fields is optional.
All models must be extends of Model and implements ModelInterface same
as in this example below:

Example of model (in namespace Model):

```php
<?php

declare(strict_types=1);

namespace Model;

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
                'validate' => 'required|minlength:1',
                'unique' => true // This field must be unique
            ]
        ];
    }

}
```
You can read more about entity field rules options on Entify project page
https://github.com/rnr1721/le7-entify (validation etc). Here it should be
said that the specific field option for this package, unlike other fields,
is "unique", that must be true or false. Also, when saving entities
the "hide" filter will be skipped.

This software is convenient to use along with a dependency container. In the
container, you can pre-configure everything, and then conveniently and easily
use it in your code using dependency injection (DI).

So, now we can use this model.

## Basic usage

```php
use Core\Database\Redbean\Db;
use Core\Database\Redbean\DbConn;
use Core\Entify\Entification;
use Core\Entify\RulesLoaderClass;
use Core\Database\Redbean\Drivers\DbSql;

// Create rules loader for classes, and set namespace for models
$loader = new RulesLoaderClass('\\Model\\');

// Create instance of Entify framework
$entification = new Entification($loader);

// Now, create array with parametres
// But you can configure DB driver with methods if need
$connectionArray = [
    'namespace' => '\\Model\\', // Importsnt! Namespace for models
    'driver' => 'mysql', // mysql, pgsql, curbid
    'host' => 'localhost', // Db host
    'port' => '3306', // Your Db port
    'user' => 'user',
    'name' => 'database',
    'pass' => '123'
];

// Now we create driver
$driver = new DbSql($connectionArray);

// Create object that connect, disconnect and switch between DBs
$connection = new DBConn($driver, $entification);

// Create database object wrapper
$db = new Db($connection);

// Code above you can run in DI container so it not scarry :)

// Now we can use non-static Redbean methods with $db object
$bean = $db->dispense('contact');

// See in model class above
$bean->name = 'John';
$bean->lastname = 'Doe';
$bean->another = '';

try {
    // Now this make invalidArgumentException because in rules another is
    // required field
    $db->store($bean);
} catch (\InvalidArgumentException $e) {
    // Get errors
    $errors = $bean->getErrors();
}

// See the errors
if (isset($errors)) {
    print_r($errors);
}

// But now, we try to make correct saving
$bean->another = '777';
$db->store($bean);

```

If you need multi-lingual validator messages, please send me translations
for this projects (or make commit requests) and I add them:
https://github.com/rnr1721/le7-validator

## Multiple entities

Now we can try to work with arrays of entities. For example, we need to paginate
SQL request or filter it after recieving.

```php
use Core\Database\Redbean\EntificationSql;
use Core\Database\Redbean\Db;
use Core\Database\Redbean\DbConn;
use Core\Entify\Entification;
use Core\Entify\RulesLoaderClass;
use Core\Database\Redbean\Drivers\DbSql;

$loader = new RulesLoaderClass('\\TestsModel\\');

$entification = new Entification($loader);

$connectionArray = [
    'namespace' => '\\Model\\', // Importsnt! Namespace for models
    'driver' => 'mysql', // mysql, pgsql, curbid
    'host' => 'localhost', // Db host
    'port' => '3306', // Your Db port
    'user' => 'user',
    'name' => 'database',
    'pass' => '123'
];

$driver = new DbSql($connectionArray);

$connection = new DBConn($driver, $entification);

$db = new Db($connection);

$entificationSql = new EntificationSql($loader, $db);

// Now we end DI container part. And now we can use repository entities

// Get repository data provider
// You can also set bindings and custom query
$provider = $entificationSql->getDataProvider('contact');

// Case1 - get paginated result:
// Per page 5 items, and current page 1
$entity = $provider->paginate(5, 1)->getEntity();
$data = $entity->export(); // Get data as array
$info = $entity->getInfo(); // Get pagination data and rules info

// Case2 - get paginated custom result
$entity = $provider->paginate(5, 1)->select()->from()->where('id = 1')->getEntity();
$data = $entity->export(); // Get data as array
$info = $entity->getInfo(); // Get pagination data and rules info
```

Of course you can use tokens, something like this:

```php
$bindings = [1,2,3];
$provider = $entificationSql->getDataProvider('contact',$bindings);
$entity = $provider->paginate(5, 1)->select()->from()->where(' id = ? OR id = ? OR id = ? ')->getEntity();
$data = $entity->export(); // Get data as array
$info = $entity->getInfo(); // Get pagination data and rules info
```
About this knows all, but for same, I strongly recommend use bindings instead direct values!

```php
// Unpaginated result
$entity = $provider->select()->from()->where('id = 1')->getEntity();
$data = $entity->export(); // Get data as array
$info = $entity->getInfo(); // Empty pagination data and rules info
```
