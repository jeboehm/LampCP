<?php

namespace Jboehm\Lampcp\CoreBundle\Service;

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

	public function addGroup($name, $gid) {

	}
}
