<?php

namespace Jboehm\Lampcp\CoreBundle\Service;

class GidService {
	protected $_gidMin;
	protected $_gidMax;
	protected $_systemGroupFile;
	protected $_groups = array();

	/**
	 * Konstruktor
	 *
	 * @param int    $gidmin
	 * @param int    $gidmax
	 * @param string $groupfile
	 */
	public function __construct($gidmin, $gidmax, $groupfile) {
		$this->_gidMin          = $gidmin;
		$this->_gidMax          = $gidmax;
		$this->_systemGroupFile = $groupfile;
		$this->_groups          = $this->_parseFile();
	}

	/**
	 * Get group file contents
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _getFile() {
		$file = file_get_contents($this->_systemGroupFile);

		if(!$file) {
			throw new \Exception('Could not read group file (' . $this->_systemGroupFile . ')!');
		}

		return $file;
	}

	/**
	 * Parses the group file
	 *
	 * @return array
	 */
	protected function _parseFile() {
		$lines  = explode(PHP_EOL, $this->_getFile());
		$groups = array();

		foreach($lines as $line) {
			$lineSplit = explode(':', $line, 4);

			if(count($lineSplit) === 4) {
				$group = array('name' => $lineSplit[0],
							   'gid'  => intval($lineSplit[2])
				);

				if(!empty($lineSplit[3])) {
					$group['members'] = explode(',', $lineSplit[3]);
				} else {
					$group['members'] = array();
				}

				$groups[] = $group;
			}
		}

		return $groups;
	}

	/**
	 * Get groups
	 *
	 * @return array
	 */
	public function getGroups() {
		return $this->_groups;
	}

	/**
	 * Get group by gid
	 *
	 * @param int $gid
	 *
	 * @return null
	 */
	public function getGroupByGid($gid) {
		foreach($this->_groups as $group) {
			if($group['gid'] === $gid) {
				return $group;
			}
		}

		return null;
	}

	/**
	 * Get an unused gid
	 * Takes care of the configured min / max gid service-arguments
	 *
	 * @return int|null
	 */
	public function getFreeGid() {
		$min = $this->_gidMin;
		$max = $this->_gidMax;

		for($i = $min; $i <= $max; $i++) {
			if(!$this->getGroupByGid($i)) {
				return $i;
			}
		}

		return null;
	}
}
