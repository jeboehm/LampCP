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

use Jboehm\Lampcp\CoreBundle\Utilities\ExecUtility;

class SysUserService {
	/** @var UidService */
	protected $_uidservice;

	/** @var GidService */
	protected $_gidservice;

	/** @var string */
	protected $_useraddcmd;

	/** @var string */
	protected $_groupaddcmd;

	/**
	 * Konstruktor
	 *
	 * @param UidService $uidservice
	 * @param GidService $gidservice
	 * @param string     $useraddcmd
	 * @param string     $groupaddcmd
	 */
	public function __construct($uidservice, $gidservice, $useraddcmd, $groupaddcmd) {
		$this->_uidservice  = $uidservice;
		$this->_gidservice  = $gidservice;
		$this->_useraddcmd  = $useraddcmd;
		$this->_groupaddcmd = $groupaddcmd;
	}

	/**
	 * Check for free gid
	 *
	 * @param int $gid
	 *
	 * @return bool
	 */
	protected function _checkGidFree($gid) {
		if(!$this->_gidservice->getGroupByGid($gid)) {
			return true;
		}

		return false;
	}

	/**
	 * Check for free uid
	 *
	 * @param int $uid
	 *
	 * @return bool
	 */
	protected function _checkUidFree($uid) {
		if(!$this->_uidservice->getUserByUid($uid)) {
			return true;
		}

		return false;
	}

	/**
	 * Add group to system
	 *
	 * @param string $name
	 * @param int    $gid
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function _addGroupCmd($name, $gid) {
		$output = ExecUtility::exec($this->_groupaddcmd, array('-g' => $gid,
															   $name));

		if($output['code'] != 0) {
			throw new \Exception('Error: Could not execute ' . $this->_groupaddcmd);
		} else {
			return true;
		}

		return false;
	}

	/**
	 * Add user to system, use $gid as primary group
	 *
	 * @param string $name
	 * @param int    $uid
	 * @param int    $gid
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function _addUserCmd($name, $uid, $gid) {
		$output = ExecUtility::exec($this->_useraddcmd, array('-u' => $uid,
															  '-g' => $gid,
															  '-M',
															  '-N',
															  $name));

		if($output['code'] != 0) {
			throw new \Exception('Error: Could not execute ' . $this->_useraddcmd);
		} else {
			return true;
		}

		return false;
	}

	/**
	 * Add group
	 *
	 * @param string $name
	 * @param int    $gid
	 *
	 * @return bool
	 */
	public function addGroup($name, $gid) {
		try {
			$result = null;
			$result = $this->_addGroupCmd($name, $gid);
		} catch(Exception $e) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Add user
	 *
	 * @param string $name
	 * @param int    $uid
	 * @param int    $gid
	 *
	 * @return bool
	 */
	public function addUser($name, $uid, $gid) {
		try {
			$result = null;
			$result = $this->_addUserCmd($name, $uid, $gid);
		} catch(Exception $e) {
			$result = false;
		}

		return $result;
	}
}
