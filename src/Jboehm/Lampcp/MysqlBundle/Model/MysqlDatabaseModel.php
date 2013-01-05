<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\MysqlBundle\Model;

class MysqlUserModel {
	/** @var string */
	private $username;

	/** @var string */
	private $password;

	/** @var string */
	private $host;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->host = 'localhost';
	}

	/**
	 * @param string $host
	 *
	 * @return MysqlUserModel
	 */
	public function setHost($host) {
		$this->host = $host;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @param string $password
	 *
	 * @return MysqlUserModel
	 */
	public function setPassword($password) {
		$this->password = $password;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $username
	 *
	 * @return MysqlUserModel
	 */
	public function setUsername($username) {
		$this->username = $username;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
}
