<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Jeboehm\Lampcp\MysqlBundle\Model\MysqlUserModel;
use Jeboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel;
use Jeboehm\Lampcp\MysqlBundle\Exception\CouldNotConnectException;
use Jeboehm\Lampcp\MysqlBundle\Exception\UserAlreadyExistsException;
use Jeboehm\Lampcp\MysqlBundle\Exception\UserNotExistsException;
use Jeboehm\Lampcp\MysqlBundle\Exception\DatabaseAlreadyExistsException;
use Jeboehm\Lampcp\MysqlBundle\Exception\DatabaseNotExistsException;

/**
 * Class MysqlAdminService
 *
 * CRUD for MySQL databases and users.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlAdminService {
    /** @var Logger */
    protected $_logger;

    /** @var \mysqli */
    protected $_mysqli;

    /**
     * Konstruktor
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->_logger = $logger;
    }

    /**
     * Connect to database.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int    $port
     */
    public function connect($host, $user, $password = '', $port = 3306) {
        if (!$this->_mysqli) {
            $this->_mysqli = $this->_createMysqlConnection($host, $user, $password, $port);
        }
    }

    /**
     * Create MySQLi Connection.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int    $port
     *
     * @throws CouldNotConnectException
     * @return \mysqli
     */
    protected function _createMysqlConnection($host, $user, $password = '', $port = 3306) {
        $mysqli = new \mysqli($host, $user, $password, null, $port);

        if ($mysqli->connect_error) {
            $this->_logger->error('Could not connect to MySQL Server: ' . $mysqli->connect_error);
            throw new CouldNotConnectException($mysqli->connect_error);
        }

        return $mysqli;
    }

    /**
     * Get MySQL Users with specified prefix.
     *
     * @param string $prefix
     *
     * @return MysqlUserModel[]
     */
    public function getUsers($prefix = '') {
        /** @var $users MysqlUserModel[] */
        $users  = array();
        $q      = sprintf('SELECT User, Host FROM mysql.user WHERE User LIKE "%s%%"', $prefix);
        $result = $this->_mysqli->query($q);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user = new MysqlUserModel();
                $user
                    ->setUsername($row['User'])
                    ->setHost($row['Host']);
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Get MySQL databases with specified prefix.
     *
     * @param string $prefix
     *
     * @return MysqlDatabaseModel[]
     */
    public function getDatabases($prefix = '') {
        /** @var $dbs MysqlDatabaseModel[] */
        $dbs    = array();
        $q      = sprintf('show databases');
        $result = $this->_mysqli->query($q);

        while ($row = $result->fetch_assoc()) {
            $db = new MysqlDatabaseModel();
            $db->setName($row['Database']);

            if (!empty($prefix)) {
                if (substr($db->getName(), 0, strlen($prefix)) === $prefix) {
                    $dbs[] = $db;
                }
            } else {
                $dbs[] = $db;
            }
        }

        return $dbs;
    }

    /**
     * Checks, if a specified user exists in MySQL.
     *
     * @param MysqlUserModel $user
     *
     * @return bool
     */
    public function checkUserExists(MysqlUserModel $user) {
        $q      = sprintf('SELECT User, Host FROM mysql.user WHERE User = "%s" AND Host = "%s"', $user->getUsername(), $user->getHost());
        $result = $this->_mysqli->query($q);

        if ($result->num_rows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Checks, if a specified database exists in MySQL.
     *
     * @param MysqlDatabaseModel $database
     *
     * @return bool
     */
    public function checkDatabaseExists(MysqlDatabaseModel $database) {
        $q      = sprintf('show databases');
        $result = $this->_mysqli->query($q);

        while ($row = $result->fetch_assoc()) {
            if ($row['Database'] === $database->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create MySQL Database.
     *
     * @param MysqlDatabaseModel $database
     *
     * @return bool
     * @throws DatabaseAlreadyExistsException
     */
    public function createDatabase(MysqlDatabaseModel $database) {
        if ($this->checkDatabaseExists($database)) {
            $this->_logger->error('MySQL database already exists: ' . $database->getName());
            throw new DatabaseAlreadyExistsException();
        }

        $q      = sprintf('CREATE DATABASE %s', $database->getName());
        $result = $this->_mysqli->query($q);

        if ($result) {
            $this->_logger->info('MySQL Database created: ' . $database->getName());

            return true;
        }

        return false;
    }

    /**
     * Drops a MySQL Database.
     *
     * @param MysqlDatabaseModel $database
     *
     * @return bool
     * @throws DatabaseNotExistsException
     */
    public function dropDatabase(MysqlDatabaseModel $database) {
        if (!$this->checkDatabaseExists($database)) {
            return true;
        }

        $q      = sprintf('DROP DATABASE %s', $database->getName());
        $result = $this->_mysqli->query($q);

        if ($result) {
            $this->_logger->info('MySQL Database deleted: ' . $database->getName());

            return true;
        }

        return false;
    }

    /**
     * Create MySQL User.
     *
     * @param MysqlUserModel $user
     *
     * @throws UserAlreadyExistsException
     * @return bool
     */
    public function createUser(MysqlUserModel $user) {
        if ($this->checkUserExists($user)) {
            $this->_logger->error('MySQL user already exists: ' . $user->getUsername());
            throw new UserAlreadyExistsException($user->getUsername());
        }

        $q      = sprintf('CREATE USER "%s"@"%s" IDENTIFIED BY "%s"', $user->getUsername(), $user->getHost(), $user->getPassword());
        $result = $this->_mysqli->query($q);

        if ($result) {
            $this->_logger->info('Created MySQL user: ' . $user->getUsername());

            return true;
        }

        return false;
    }

    /**
     * Drop MySQL User.
     *
     * @param MysqlUserModel $user
     *
     * @return bool
     * @throws UserNotExistsException
     */
    public function dropUser(MysqlUserModel $user) {
        if (!$this->checkUserExists($user)) {
            return true;
        }

        $q      = sprintf('DROP USER "%s"@"%s"', $user->getUsername(), $user->getHost());
        $result = $this->_mysqli->query($q);

        if ($result) {
            $this->_logger->info('Deleted MySQL user: ' . $user->getUsername());

            return true;
        }

        return false;
    }

    /**
     * Set MySQL User password.
     *
     * @param MysqlUserModel $user
     *
     * @return bool
     * @throws UserNotExistsException
     */
    public function setUserPassword(MysqlUserModel $user) {
        if (!$this->checkUserExists($user)) {
            $this->_logger->error('MySQL user not exists: ' . $user->getUsername());
            throw new UserNotExistsException();
        }

        $q      = sprintf('SET PASSWORD FOR "%s"@"%s" = PASSWORD("%s")', $user->getUsername(), $user->getHost(), $user->getPassword());
        $result = $this->_mysqli->query($q);

        if ($result) {
            $this->_logger->info('(MysqlBundle) Changed MySQL user password: ' . $user->getUsername());

            return true;
        }

        return false;
    }

    /**
     * Grant permissions on database for MySQL User
     *
     * @param MysqlDatabaseModel $database
     *
     * @return bool
     * @throws DatabaseNotExistsException
     */
    public function grantPermissionsOnDatabase(MysqlDatabaseModel $database) {
        if (!$this->checkDatabaseExists($database)) {
            $this->_logger->error('MySQL Database not exists: ' . $database->getName());
            throw new DatabaseNotExistsException();
        }

        foreach ($database->getUsers() as $user) {
            $q      = sprintf('GRANT %s ON %s.* TO "%s"@"%s"', join(', ', $database->getPermission()), $database->getName(), $user->getUsername(), $user->getHost());
            $result = $this->_mysqli->query($q);

            if (!$result) {
                $this->_logger->error('Could not grant permissions on MySQL database: ' . $database->getName());
            }
        }

        $this->_logger->info('Granted permissions on MySQL database: ' . $database->getName());

        return true;
    }
}
