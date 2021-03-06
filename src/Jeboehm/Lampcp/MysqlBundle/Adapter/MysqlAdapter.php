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

        return true && $this->updateDatabasePermission($database);
    }

    /**
     * Update the database permissions.
     *
     * @param Database $database
     *
     * @return bool
     */
    protected function updateDatabasePermission(Database $database)
    {
        $conn                 = $this->connection->getConnection();
        $currentDatabase      = $this
            ->getDatabases()
            ->findByName($database->getName());
        $currentUsersIterator = $currentDatabase
            ->getUsers()
            ->getIterator();
        $newUsersIterator     = $database
            ->getUsers()
            ->getIterator();

        // Revoke all permissions.
        try {
            foreach ($currentUsersIterator as $user) {
                /** @var User $user */

                $conn->executeQuery(
                    sprintf('REVOKE ALL PRIVILEGES ON %s.* FROM ?@?', $database->getName()),
                    array(
                         $user->getName(),
                         $user->getHost(),
                    )
                );
            }
        } catch (DBALException $e) {
            return false;
        }

        // Add permissions.
        try {
            foreach ($newUsersIterator as $user) {
                /** @var User $user */

                $conn->executeQuery(
                    sprintf(
                        'GRANT %s ON %s.* TO ?@?',
                        join(', ', $this->getDefaultPermissions()),
                        $database->getName()
                    ),
                    array(
                         $user->getName(),
                         $user->getHost(),
                    )
                );
            }
        } catch (DBALException $e) {
            return false;
        }

        return true;
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
        $dbname = str_replace('_', '\_', $database->getName());
        $added  = array();
        $result = $this->connection
            ->getConnection()
            ->fetchAll(
                'SELECT Db, User, Host FROM mysql.db WHERE Db = ? OR Db = ?',
                array(
                     $dbname,
                     $database->getName(),
                )
            );

        foreach ($result as $row) {
            $username = $row['User'];
            $host     = $row['Host'];

            if (!in_array($username . $host, $added) && !empty($username)) {
                $user = new User();
                $user
                    ->setName($username)
                    ->setHost($host);

                $database->addUser($user);
                $added[] = $username . $host;
            }
        }

        return $database;
    }

    /**
     * Get an array of default permissions for
     * databases.
     *
     * @return array
     */
    protected function getDefaultPermissions()
    {
        return array(
            'SELECT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'CREATE',
            'DROP',
            'ALTER',
            'INDEX',
        );
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
        $permissions = $this->updateDatabasePermission($database);

        return $permissions;
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
        try {
            $this->connection
                ->getConnection()
                ->executeQuery(
                    'CREATE USER ?@? IDENTIFIED BY ?',
                    array(
                         $user->getName(),
                         $user->getHost(),
                         $user->getPassword(),
                    )
                );
        } catch (DBALException $e) {
            return false;
        }

        return true;
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
        try {
            $this->connection
                ->getConnection()
                ->executeQuery(
                    'DROP USER ?@?',
                    array(
                         $user->getName(),
                         $user->getHost(),
                    )
                );
        } catch (DBALException $e) {
            return false;
        }

        return true;
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
        try {
            $this->connection
                ->getConnection()
                ->executeQuery(
                    'SET PASSWORD FOR ?@? = PASSWORD(?)',
                    array(
                         $user->getName(),
                         $user->getHost(),
                         $user->getPassword(),
                    )
                );
        } catch (DBALException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get users.
     *
     * @return User[]
     */
    public function getUsers()
    {
        $added  = array();
        $models = new UserCollection();
        $result = $this->connection
            ->getConnection()
            ->fetchAll('SELECT User, Host FROM mysql.user');

        foreach ($result as $row) {
            $username = $row['User'];
            $host     = $row['Host'];

            if (!in_array($username . $host, $added) && !empty($username)) {
                $user = new User();
                $user
                    ->setName($username)
                    ->setHost($host);

                $models->add($user);
                $added[] = $username . $host;
            }
        }

        return $models;
    }
}
