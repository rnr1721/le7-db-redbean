<?php

declare(strict_types=1);

namespace Core\Database\Redbean\Interfaces;

use Core\Database\Redbean\Interfaces\DbConnInterface;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

/**
 * This is wrapper for RedbeanPHP database methods, duch as find, findAll,
 * getAll etc. It allow to use arrow method calls instead static methods
 * as in RedbeanPHP for more information of redbean methods see
 * https://redbeanphp.com and https://github.com/gabordemooij/redbean
 */
interface DbInterface
{

    /**
     * Get database connection object
     * for switching between databases, indicate connection statis
     * or disconnecting
     * @return DbConnInterface Database connection object
     */
    public function getConnection(): DbConnInterface;

    /**
     * Loads a bean from the object database.
     * It searches for a OODBBean Bean Object in the
     * database. It does not matter how this bean has been stored.
     * RedBean uses the primary key ID $id and the string $type
     * to find the bean. The $type specifies what kind of bean you
     * are looking for; this is the same type as used with the
     * dispense() function. If RedBean finds the bean it will return
     * the OODB Bean object; if it cannot find the bean
     * RedBean will return a new bean of type $type and with
     * primary key ID 0. In the latter case it acts basically the
     * same as dispense().
     *
     * Important note:
     * If the bean cannot be found in the database a new bean of
     * the specified type will be generated and returned.
     *
     * Usage:
     *
     * <code>
     * $post = $db->dispense('post');
     * $post->title = 'my post';
     * $id = $db->store($post);
     * $post = $db->load('post', $id);
     * $db->trash($post);
     * </code>
     * 
     * @param string $type type of bean you want to load
     * @param int $id ID of the bean you want to load
     * @param string $snippet string to use after select  (optional)
     * @return OODBBean Bean
     */
    public function load(string $type, int $id, string $snippet = null): OODBBean;

    /**
     * Convenience function to fire an SQL query using the RedBeanPHP
     * database adapter. This method allows you to directly query the
     * database without having to obtain an database adapter instance first.
     * Executes the specified SQL query together with the specified
     * parameter bindings and returns all rows
     * and all columns.
     * 
     * @param string $sql SQL query
     * @param array $bindings a list of values to be bound to query parameters
     * @return array Result array
     */
    public function getAll(string $sql, array $bindings = array()): array;

    /**
     * Convenience function to fire an SQL query using the RedBeanPHP
     * database adapter. This method allows you to directly query the
     * database without having to obtain an database adapter instance first.
     * Executes the specified SQL query together with the specified
     * parameter bindings and returns a single cell.
     * 
     * @param string $sql Sql query
     * @param array $bindings a list of values to be bound to query parameters
     * @return string|null String or null
     */
    public function getCell(string $sql, array $bindings = array()): string|null;

    /**
     * Convenience function to fire an SQL query using the RedBeanPHP
     * database adapter. This method allows you to directly query the
     * database without having to obtain an database adapter instance first.
     * Executes the specified SQL query together with the specified
     * parameter bindings and returns a single row.
     * 
     * @param string $sql SQL query
     * @param array $bindings a list of values to be bound to query parameters
     * @return array|null Array or null
     */
    public function getRow(string $sql, array $bindings = array()): array|null;

    /**
     * Like R::find() but returns the first bean only.
     * 
     * @param string $type the type of bean you are looking for
     * @param string $sql SQL query to find the desired bean, starting right after WHERE clause
     * @param array $bindings array of values to be bound to parameters in query
     * @return OODBBean|null Bean or null
     */
    public function findOne(string $type, string $sql = null, array $bindings = array()): OODBBean|null;

    /**
     * Alias for find().
     * 
     * @param string $type the type of bean you are looking for
     * @param string $sql SQL query to find the desired bean, starting right after WHERE clause
     * @param array $bindings array of values to be bound to parameters in query
     * @return array Array of beans
     */
    public function findAll(string $type, string $sql = null, array $bindings = array()): array;

    /**
     * Dispenses a new RedBean OODB Bean for use with
     * the rest of the methods. RedBeanPHP thinks in beans, the bean is the
     * primary way to interact with RedBeanPHP and the database managed by
     * RedBeanPHP. To load, store and delete data from the database using RedBeanPHP
     * you exchange these RedBeanPHP OODB Beans. The only exception to this rule
     * are the raw query methods like R::getCell() or R::exec() and so on.
     * The dispense method is the 'preferred way' to create a new bean.
     *
     * Usage:
     *
     * <code>
     * $book = $db->dispense( 'book' );
     * $book->title = 'My Book';
     * $db->store( $book );
     * </code>
     *
     * This method can also be used to create an entire bean graph at once.
     * Given an array with keys specifying the property names of the beans
     * and a special _type key to indicate the type of bean, one can
     * make the Dispense Helper generate an entire hierarchy of beans, including
     * lists. To make dispense() generate a list, simply add a key like:
     * ownXList or sharedXList where X is the type of beans it contains and
     * a set its value to an array filled with arrays representing the beans.
     * Note that, although the type may have been hinted at in the list name,
     * you still have to specify a _type key for every bean array in the list.
     * Note that, if you specify an array to generate a bean graph, the number
     * parameter will be ignored.
     *
     * Usage:
     *
     * <code>
     *  $book = $db->dispense( [
     *   '_type' => 'book',
     *   'title'  => 'Gifted Programmers',
     *   'author' => [ '_type' => 'author', 'name' => 'Xavier' ],
     *   'ownPageList' => [ ['_type'=>'page', 'text' => '...'] ]
     * ] );
     * </code>
     *
     * @param string|OODBBean[] $typeOrBeanArray   type or bean array to import
     * @param int $num number of beans to dispense
     * @param bool $alwaysReturnArray if TRUE always returns the result as an array
     *
     * @return OODBBean|OODBBean[]
     */
    public function dispense(string|array $typeOrBeanArray, int $num = 1, bool $alwaysReturnArray = FALSE): array|OODBBean;

    /**
     * Stores a bean in the database. This method takes a
     * OODBBean Bean Object $bean and stores it
     * in the database. If the database schema is not compatible
     * with this bean and RedBean runs in fluid mode the schema
     * will be altered to store the bean correctly.
     * If the database schema is not compatible with this bean and
     * RedBean runs in frozen mode it will throw an exception.
     * This function returns the primary key ID of the inserted
     * bean.
     *
     * The return value is an integer if possible. If it is not possible to
     * represent the value as an integer a string will be returned.
     *
     * Usage:
     *
     * <code>
     * $post = $db->dispense('post');
     * $post->title = 'my post';
     * $id = $db->store( $post );
     * $post = $db->load( 'post', $id );
     * $db->trash( $post );
     * </code>
     *
     * In the example above, we create a new bean of type 'post'.
     * We then set the title of the bean to 'my post' and we
     * store the bean. The store() method will return the primary
     * key ID $id assigned by the database. We can now use this
     * ID to load the bean from the database again and delete it.
     *
     * If the second parameter is set to TRUE and
     * Hybrid mode is allowed (default OFF for novice), then RedBeanPHP
     * will automatically temporarily switch to fluid mode to attempt to store the
     * bean in case of an SQLException.
     *
     * @param OODBBean|SimpleModel $bean bean to store
     * @param bool $unfreezeIfNeeded retries in fluid mode in hybrid mode
     *
     * @return int|string
     */
    public function store(OODBBean|SimpleModel $bean, bool $unfreezeIfNeeded = FALSE): int|string;

    /**
     * Removes a bean from the database.
     * This function will remove the specified OODBBean
     * Bean Object from the database.
     *
     * This facade method also accepts a type-id combination,
     * in the latter case this method will attempt to load the specified bean
     * and THEN trash it.
     *
     * Usage:
     *
     * <code>
     * $post = $db->dispense('post');
     * $post->title = 'my post';
     * $id = $db->store( $post );
     * $post = $db->load( 'post', $id );
     * $db->trash( $post );
     * </code>
     *
     * In the example above, we create a new bean of type 'post'.
     * We then set the title of the bean to 'my post' and we
     * store the bean. The store() method will return the primary
     * key ID $id assigned by the database. We can now use this
     * ID to load the bean from the database again and delete it.
     *
     * @param string|OODBBean|SimpleModel $beanOrType bean you want to remove from database
     * @param int $id ID if the bean to trash (optional, type-id variant only)
     *
     * @return int
     */
    public function trash(string|OODBBean|SimpleModel $beanOrType, int $id = null): int;

    /**
     * Short hand function to trash a set of beans at once.
     * For information please consult the R::trash() function.
     * A loop saver.
     *
     * @param OODBBean[] $beans list of beans to be trashed
     *
     * @return int
     */
    public function trashAll(array $beans): int;

    /**
     * Inspects the database schema. If you pass the type of a bean this
     * method will return the fields of its table in the database.
     * The keys of this array will be the field names and the values will be
     * the column types used to store their values.
     * If no type is passed, this method returns a list of all tables in the database.
     *
     * @param string|NULL $type Type of bean (i.e. table) you want to inspect, or NULL for a list of all tables
     *
     * @return string[]
     */
    public function inspect(string|null $type = null): array;

    /**
     * Returns an array of beans. Pass a type and a series of ids and
     * this method will bring you the corresponding beans.
     *
     * important note: Because this method loads beans using the load()
     * function (but faster) it will return empty beans with ID 0 for
     * every bean that could not be located. The resulting beans will have the
     * passed IDs as their keys.
     *
     * @param string $type type of beans
     * @param int[] $ids  ids to load
     *
     * @return OODBBean[]
     */
    public function batch(string $type, array $ids): array;

    /**
     * Short hand function to store a set of beans at once, IDs will be
     * returned as an array. For information please consult the R::store()
     * function.
     * A loop saver.
     *
     * If the second parameter is set to TRUE and
     * Hybrid mode is allowed (default OFF for novice), then RedBeanPHP
     * will automatically temporarily switch to fluid mode to attempt to store the
     * bean in case of an SQLException.
     *
     * @param OODBBean[] $beans list of beans to be stored
     * @param bool $unfreezeIfNeeded retries in fluid mode in hybrid mode
     *
     * @return int[] ids
     */
    public function storeAll(array $beans, bool $unfreezeIfNeeded = FALSE): array;

    /**
     * Counts the number of beans of type $type.
     * This method accepts a second argument to modify the count-query.
     * A third argument can be used to provide bindings for the SQL snippet.
     *
     * @param string $type type of bean we are looking for
     * @param string $addSQL additional SQL snippet
     * @param array $bindings parameters to bind to SQL
     *
     * @return int
     */
    public function count(string $type, string $addSQL = '', array $bindings = array()): int;

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql       SQL query to execute
     * @param array  $bindings  a list of values to be bound to query parameters
     *
     * @return int
     */
    public function exec(string $sql, array $bindings = array()): int;

    /**
     * Generates question mark slots for an array of values.
     * Given an array and an optional template string this method
     * will produce string containing parameter slots for use in
     * an SQL query string.
     *
     * Usage:
     *
     * <code>
     * $db->genSlots( array( 'a', 'b' ) );
     * </code>
     *
     * The statement in the example will produce the string:
     * '?,?'.
     *
     * Another example, using a template string:
     *
     * <code>
     * $db->genSlots( array('a', 'b'), ' IN( %s ) ' );
     * </code>
     *
     * The statement in the example will produce the string:
     * ' IN( ?,? ) '.
     *
     * @param array $array array to generate question mark slots for
     * @param string|null $template template to use
     *
     * @return string
     */
    public function genSlots(array $array, string $template = null): string;
}
