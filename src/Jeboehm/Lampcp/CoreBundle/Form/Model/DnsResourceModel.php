<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form\Model;

class DnsResourceModel {
	/** @var string */
	private $_name;

	/** @var integer */
	private $_ttl;

	/** @var string */
	private $_type;

	/** @var string */
	private $_rdata;

	/**
	 * Set Name
	 *
	 * @param string $name
	 *
	 * @return DnsResourceModel
	 */
	public function setName($name) {
		$this->_name = $name;

		return $this;
	}

	/**
	 * Get Name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Set Resource Data
	 *
	 * @param string $rdata
	 *
	 * @return DnsResourceModel
	 */
	public function setRdata($rdata) {
		$this->_rdata = $rdata;

		return $this;
	}

	/**
	 * Get Resource Data
	 *
	 * @return string
	 */
	public function getRdata() {
		return $this->_rdata;
	}

	/**
	 * Set TTL
	 *
	 * @param int $ttl
	 *
	 * @return DnsResourceModel
	 */
	public function setTtl($ttl) {
		$this->_ttl = $ttl;

		return $this;
	}

	/**
	 * Get TTL
	 *
	 * @return int
	 */
	public function getTtl() {
		return $this->_ttl;
	}

	/**
	 * Set Type
	 *
	 * @param string $type
	 *
	 * @return DnsResourceModel
	 */
	public function setType($type) {
		$this->_type = $type;

		return $this;
	}

	/**
	 * Get Type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}
}
