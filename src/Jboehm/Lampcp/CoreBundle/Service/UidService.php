<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Service;

class UidService {
	protected $_uidMin;
	protected $_uidMax;
	protected $_systemUserFile;
	protected $_users = array();

	/**
	 * Konstruktor
	 *
	 * @param $uidmin
	 * @param $uidmax
	 * @param $userfile
	 */
	public function __construct($uidmin, $uidmax, $userfile) {
		$this->_uidMin         = $uidmin;
		$this->_uidMax         = $uidmax;
		$this->_systemUserFile = $userfile;
		$this->_users          = $this->_parseFile();
	}

	/**
	 * Get user file contents
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _getFile() {
		$file = file_get_contents($this->_systemUserFile);

		if(!$file) {
			throw new \Exception('Could not read user file (' . $this->_systemUserFile . ')!');
		}

		return $file;
	}

	/**
	 * Parses the user file
	 *
	 * @return array
	 */
	protected function _parseFile() {
		$lines = explode(PHP_EOL, $this->_getFile());
		$users = array();

		foreach($lines as $line) {
			$lineSplit = explode(':', $line, 7);

			if(count($lineSplit) === 7) {
				$user = array('name'  => $lineSplit[0],
							  'uid'   => intval($lineSplit[2]),
							  'gid'   => intval($lineSplit[3]),
							  'gecos' => $lineSplit[4],
							  'home'  => $lineSplit[5],
							  'shell' => $lineSplit[6]
				);

				$users[] = $user;
			}
		}

		return $users;
	}

	/**
	 * Get users
	 *
	 * @return array
	 */
	public function getUsers() {
		return $this->_users;
	}

	/**
	 * Get user by uid
	 *
	 * @param int $uid
	 *
	 * @return null
	 */
	public function getUserByUid($uid) {
		foreach($this->_users as $user) {
			if($user['uid'] === $uid) {
				return $user;
			}
		}

		return null;
	}

	/**
	 * Get user by name
	 *
	 * @param string $name
	 *
	 * @return null
	 */
	public function getUserByName($name) {
		foreach($this->_users as $user) {
			if($user['name'] === $name) {
				return $user;
			}
		}

		return null;
	}

	/**
	 * Get an unused uid
	 * Takes care of the configured min / max uid service-arguments
	 *
	 * @return int|null
	 */
	public function getFreeUid() {
		$min = $this->_uidMin;
		$max = $this->_uidMax;

		for($i = $min; $i <= $max; $i++) {
			if(!$this->getUserByUid($i)) {
				return $i;
			}
		}

		return null;
	}
}
