<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\MysqlBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Jboehm\Lampcp\MysqlBundle\Model\MysqlUserModel;
use Jboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel;
use Jboehm\Lampcp\MysqlBundle\Exception\UserAlreadyExistsException;
use Jboehm\Lampcp\MysqlBundle\Exception\UserNotExistsException;
use Jboehm\Lampcp\MysqlBundle\Exception\DatabaseAlreadyExistsException;
use Jboehm\Lampcp\MysqlBundle\Exception\DatabaseNotExistsException;

class MysqlAdminService {
	/** @var Logger */
	protected $_logger;

	/** @var \mysqli */
	protected $_mysqli;

	/**
	 * Konstruktor
	 *
	 * @param \Symfony\Bridge\Monolog\Logger $logger
	 */
	public function __construct(Logger $logger) {
		$this->_logger = $logger;
	}

	/**
	 * Connect
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param int    $port
	 */
	public function connect($host, $user, $password = '', $port = 3306) {
		$this->_mysqli = $this->_createMysqlConnection($host, $user, $password, $port);
	}

	/**
	 * Create MySQLi Connection
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param int    $port
	 *
	 * @return \mysqli
	 * @throws \Exception
	 */
	protected function _createMysqlConnection($host, $user, $password = '', $port = 3306) {
		$mysqli = new \mysqli($host, $user, $password, null, $port);

		if($mysqli->connect_error) {
			$msg = '(MysqlAdminService) Conn Error: ' . $mysqli->connect_error;

			$this->_logger->err($msg);
			throw new \Exception($msg);
		}

		return $mysqli;
	}

	/**
	 * Checks, if a specified user exists in MySQL
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlUserModel $user
	 *
	 * @return bool
	 */
	public function checkUserExists(MysqlUserModel $user) {
		$q      = sprintf('SELECT User, Host FROM mysql.user WHERE User = "%s" AND Host = "%s"',
			$user->getUsername(), $user->getHost());
		$result = $this->_mysqli->query($q);

		if($result->num_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * Checks, if a specified database exists in MySQL
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel $database
	 *
	 * @return bool
	 */
	public function checkDatabaseExists(MysqlDatabaseModel $database) {
		$q      = sprintf('show databases');
		$result = $this->_mysqli->query($q);

		while($row = $result->fetch_assoc()) {
			if($row['Database'] === $database->getName()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Create MySQL Database
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel $database
	 *
	 * @return bool
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\DatabaseAlreadyExistsException
	 */
	public function createDatabase(MysqlDatabaseModel $database) {
		if($this->checkDatabaseExists($database)) {
			$this->_logger->err('(MysqlAdminService) Database already exists: ' . $database->getName());
			throw new DatabaseAlreadyExistsException();
		}

		$q      = sprintf('CREATE DATABASE %s', $database->getName());
		$result = $this->_mysqli->query($q);

		if($result) {
			$this->_logger->info('(MysqlAdminService) Created database: ' . $database->getName());

			return true;
		}

		return false;
	}

	/**
	 * Drops a MySQL Database
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel $database
	 *
	 * @return bool
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\DatabaseNotExistsException
	 */
	public function dropDatabase(MysqlDatabaseModel $database) {
		if(!$this->checkDatabaseExists($database)) {
			$this->_logger->err('(MysqlAdminService) Database not exists: ' . $database->getName());
			throw new DatabaseNotExistsException();
		}

		$q      = sprintf('DROP DATABASE %s', $database->getName());
		$result = $this->_mysqli->query($q);

		if($result) {
			$this->_logger->info('(MysqlAdminService) Deleted database: ' . $database->getName());

			return true;
		}

		return false;
	}

	/**
	 * Create MySQL User
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlUserModel $user
	 *
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\UserAlreadyExistsException
	 * @return bool
	 */
	public function createUser(MysqlUserModel $user) {
		if($this->checkUserExists($user)) {
			$this->_logger->err('(MysqlAdminService) User already exists: ' . $user->getUsername());
			throw new UserAlreadyExistsException();
		}

		$q      = sprintf('CREATE USER "%s"@"%s" IDENTIFIED BY "%s"',
			$user->getUsername(), $user->getHost(), $user->getPassword());
		$result = $this->_mysqli->query($q);

		if($result) {
			$this->_logger->info('(MysqlAdminService) Created user: ' . $user->getUsername());

			return true;
		}

		return false;
	}

	/**
	 * Drop MySQL User
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlUserModel $user
	 *
	 * @return bool
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\UserNotExistsException
	 */
	public function dropUser(MysqlUserModel $user) {
		if(!$this->checkUserExists($user)) {
			$this->_logger->err('(MysqlAdminService) User not exists: ' . $user->getUsername());
			throw new UserNotExistsException();
		}

		$q      = sprintf('DROP USER "%s"@"%s"',
			$user->getUsername(), $user->getHost());
		$result = $this->_mysqli->query($q);

		if($result) {
			$this->_logger->info('(MysqlAdminService) Deleted user: ' . $user->getUsername());

			return true;
		}

		return false;
	}

	/**
	 * Set MySQL User password
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlUserModel $user
	 *
	 * @return bool
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\UserNotExistsException
	 */
	public function setUserPassword(MysqlUserModel $user) {
		if(!$this->checkUserExists($user)) {
			$this->_logger->err('(MysqlAdminService) User not exists: ' . $user->getUsername());
			throw new UserNotExistsException();
		}

		$q      = sprintf('SET PASSWORD FOR "%s"@"%s" = PASSWORD("%s")',
			$user->getUsername(), $user->getHost(), $user->getPassword());
		$result = $this->_mysqli->query($q);

		if($result) {
			$this->_logger->info('(MysqlAdminService) Changed user password: ' . $user->getUsername());

			return true;
		}

		return false;
	}

	/**
	 * Grant permissions on database for MySQL User
	 *
	 * @param \Jboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel $database
	 *
	 * @return bool
	 * @throws \Jboehm\Lampcp\MysqlBundle\Exception\DatabaseNotExistsException
	 */
	public function grantPermissionsOnDatabase(MysqlDatabaseModel $database) {
		if(!$this->checkDatabaseExists($database)) {
			$this->_logger->err('(MysqlAdminService) Database not exists: ' . $database->getName());
			throw new DatabaseNotExistsException();
		}

		foreach($database->getUsers() as $user) {
			$q      = sprintf('GRANT %s ON %s.* TO "%s"@"%s"',
				join(', ', $database->getPermission()), $database->getName(), $user->getUsername(),
				$user->getHost());
			$result = $this->_mysqli->query($q);

			if(!$result) {
				$this->_logger->err('(MysqlAdminService) Could not grant permissions on database: ' . $database->getName());
			}
		}

		$this->_logger->info('(MysqlAdminService) Granted permissions on database: ' . $database->getName());

		return true;
	}
}
