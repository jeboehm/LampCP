<?php

namespace Jboehm\Lampcp\CoreBundle\Service;

class GidService {
	protected $_gidMin;
	protected $_gidMax;
	protected $_systemGroupFile;

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
}
