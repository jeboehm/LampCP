<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Model;

use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;

class Vhost {
	/** @var string */
	private $serveradmin;

	/** @var string */
	private $servername;

	/** @var string */
	private $serveralias;

	/** @var string */
	private $suexecuser;

	/** @var string */
	private $suexecgroup;

	/** @var string */
	private $root;

	/** @var string */
	private $docroot;

	/** @var string */
	private $directoryindex;

	/** @var string */
	private $fcgiwrapper;

	/** @var string */
	private $errorlog;

	/** @var string */
	private $customlog;

	/** @var string */
	private $custom;

	/** @var IpAddress[] */
	private $ipaddress;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->serveradmin    = 'none@example.com';
		$this->directoryindex = 'index.html index.htm index.php';
	}

	/**
	 * @param string $customlog
	 *
	 * @return Vhost
	 */
	public function setCustomlog($customlog) {
		$this->customlog = $customlog;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCustomlog() {
		return $this->customlog;
	}

	/**
	 * @param string $directoryindex
	 *
	 * @return Vhost
	 */
	public function setDirectoryindex($directoryindex) {
		$this->directoryindex = $directoryindex;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDirectoryindex() {
		return $this->directoryindex;
	}

	/**
	 * @param string $root
	 *
	 * @return Vhost
	 */
	public function setRoot($root) {
		$this->root = $root;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRoot() {
		return $this->root;
	}

	/**
	 * @param string $docroot
	 *
	 * @return Vhost
	 */
	public function setDocroot($docroot) {
		$this->docroot = $docroot;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDocroot() {
		return $this->docroot;
	}

	/**
	 * @param string $errorlog
	 *
	 * @return Vhost
	 */
	public function setErrorlog($errorlog) {
		$this->errorlog = $errorlog;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getErrorlog() {
		return $this->errorlog;
	}

	/**
	 * @param string $fcgiwrapper
	 *
	 * @return Vhost
	 */
	public function setFcgiwrapper($fcgiwrapper) {
		$this->fcgiwrapper = $fcgiwrapper;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFcgiwrapper() {
		return $this->fcgiwrapper;
	}

	/**
	 * @param string $serveradmin
	 *
	 * @return Vhost
	 */
	public function setServeradmin($serveradmin) {
		$this->serveradmin = $serveradmin;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getServeradmin() {
		return $this->serveradmin;
	}

	/**
	 * @param string $serveralias
	 *
	 * @return Vhost
	 */
	public function setServeralias($serveralias) {
		$this->serveralias = $serveralias;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getServeralias() {
		return $this->serveralias;
	}

	/**
	 * @param string $servername
	 *
	 * @return Vhost
	 */
	public function setServername($servername) {
		$this->servername = $servername;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getServername() {
		return $this->servername;
	}

	/**
	 * @param string $suexecgroup
	 *
	 * @return Vhost
	 */
	public function setSuexecgroup($suexecgroup) {
		$this->suexecgroup = $suexecgroup;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSuexecgroup() {
		return $this->suexecgroup;
	}

	/**
	 * @param string $suexecuser
	 *
	 * @return Vhost
	 */
	public function setSuexecuser($suexecuser) {
		$this->suexecuser = $suexecuser;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSuexecuser() {
		return $this->suexecuser;
	}

	/**
	 * @param string $custom
	 *
	 * @return Vhost
	 */
	public function setCustom($custom) {
		$this->custom = $custom;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCustom() {
		return $this->custom;
	}

	/**
	 * @param array $ipaddress
	 *
	 * @return Vhost
	 */
	public function setIpaddress($ipaddress) {
		$this->ipaddress = $ipaddress;

		return $this;
	}

	/**
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\IpAddress[]
	 */
	public function getIpaddress() {
		return $this->ipaddress;
	}
}
