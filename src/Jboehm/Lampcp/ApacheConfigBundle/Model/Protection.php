<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Model;

class Protection {
	/** @var string */
	private $username;

	/** @var string */
	private $password;

	/**
	 * @param string $username
	 *
	 * @return Protection
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

	/**
	 * @param string $password
	 *
	 * @return Protection
	 */
	public function setPassword($password) {
		$this->password = $password;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return crypt($this->password, base64_encode($this->password));
	}
}