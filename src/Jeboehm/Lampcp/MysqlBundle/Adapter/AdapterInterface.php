<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Adapter;

use Jeboehm\Lampcp\MysqlBundle\Collection\DatabaseCollection;
use Jeboehm\Lampcp\MysqlBundle\Collection\UserCollection;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\ConnectionInterface;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;

/**
 * Interface AdapterInterface
 *
 * Adapter for database administration.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Adapter
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface AdapterInterface
{
    /**
     * Set connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * Create database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function createDatabase(Database $database);

    /**
     * Delete database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function deleteDatabase(Database $database);

    /**
     * Update database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function updateDatabase(Database $database);

    /**
     * Create user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function createUser(User $user);

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function deleteUser(User $user);

    /**
     * Update user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function updateUser(User $user);

    /**
     * Get users.
     *
     * @return UserCollection
     */
    public function getUsers();

    /**
     * Get databases.
     *
     * @return DatabaseCollection
     */
    public function getDatabases();
}
