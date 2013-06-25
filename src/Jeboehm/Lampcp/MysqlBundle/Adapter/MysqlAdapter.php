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

use Doctrine\DBAL\DBALException;
use Jeboehm\Lampcp\MysqlBundle\Collection\DatabaseCollection;
use Jeboehm\Lampcp\MysqlBundle\Collection\UserCollection;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\ConnectionInterface;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\MysqlConnection;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;

/**
 * Class MysqlAdapter
 *
 * Administrate a mysql daemon.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Adapter
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlAdapter implements AdapterInterface
{
    /** @var MysqlConnection */
    private $connection;

    /**
     * Set connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Create database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function createDatabase(Database $database)
    {
        try {
            $this->connection
                ->getConnection()
                ->getSchemaManager()
                ->createDatabase($database->getName());
        } catch (DBALException $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function deleteDatabase(Database $database)
    {
        try {
            $this->connection
                ->getConnection()
                ->getSchemaManager()
                ->dropDatabase($database->getName());
        } catch (DBALException $e) {
            return false;
        }

        return true;
    }

    /**
     * Update database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function updateDatabase(Database $database)
    {
        // TODO: Implement updateDatabase() method.
    }

    /**
     * Create user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function createUser(User $user)
    {
        // TODO: Implement createUser() method.
    }

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function deleteUser(User $user)
    {
        // TODO: Implement deleteUser() method.
    }

    /**
     * Update user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function updateUser(User $user)
    {
        // TODO: Implement updateUser() method.
    }

    /**
     * Get users.
     *
     * @return User[]
     */
    public function getUsers()
    {
        $usernames = array();
        $models    = new UserCollection();
        $result    = $this->connection
            ->getConnection()
            ->fetchAll('SELECT User FROM mysql.user');

        foreach ($result as $row) {
            $username = $row['User'];

            if (!in_array($username, $usernames) && !empty($username)) {
                $user = new User();
                $user->setName($username);

                $models->add($user);
                $usernames[] = $username;
            }
        }

        return $models;
    }

    /**
     * Get databases.
     *
     * @return DatabaseCollection
     */
    public function getDatabases()
    {
        $models = new DatabaseCollection();
        $result = $this->connection
            ->getConnection()
            ->fetchAll('SHOW DATABASES');

        foreach ($result as $dbrow) {
            $database = new Database();
            $database->setName($dbrow['Database']);

            $this->addPrivilegedUsersToDatabaseModel($database);

            $models->add($database);
        }

        return $models;
    }

    /**
     * Add users, who have access to the given database
     * to its database model.
     *
     * @param Database $database
     *
     * @return Database
     */
    protected function addPrivilegedUsersToDatabaseModel(Database $database)
    {
        $dbname    = str_replace('_', '\_', $database->getName());
        $usernames = array();
        $result    = $this->connection
            ->getConnection()
            ->fetchAll(
                'SELECT Db, User, Host FROM mysql.db WHERE Db = ?',
                array($dbname)
            );

        foreach ($result as $row) {
            $username = $row['User'];

            if (!in_array($username, $usernames) && !empty($username)) {
                $user = new User();
                $user->setName($username);

                $database->addUser($user);
                $usernames[] = $username;
            }
        }

        return $database;
    }
}
