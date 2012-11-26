<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domain
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Domain {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="domain", type="string", length=100)
	 */
	private $domain;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasWeb", type="boolean")
	 */
	private $hasWeb;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasMail", type="boolean")
	 */
	private $hasMail;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasSSH", type="boolean")
	 */
	private $hasSSH;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="gid", type="integer")
	 */
	private $gid;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set domain
	 *
	 * @param string $domain
	 *
	 * @return Domain
	 */
	public function setDomain($domain) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Get domain
	 *
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set hasWeb
	 *
	 * @param boolean $hasWeb
	 *
	 * @return Domain
	 */
	public function setHasWeb($hasWeb) {
		$this->hasWeb = $hasWeb;

		return $this;
	}

	/**
	 * Get hasWeb
	 *
	 * @return boolean
	 */
	public function getHasWeb() {
		return $this->hasWeb;
	}

	/**
	 * Set hasMail
	 *
	 * @param boolean $hasMail
	 *
	 * @return Domain
	 */
	public function setHasMail($hasMail) {
		$this->hasMail = $hasMail;

		return $this;
	}

	/**
	 * Get hasMail
	 *
	 * @return boolean
	 */
	public function getHasMail() {
		return $this->hasMail;
	}

	/**
	 * Set hasSSH
	 *
	 * @param boolean $hasSSH
	 *
	 * @return Domain
	 */
	public function setHasSSH($hasSSH) {
		$this->hasSSH = $hasSSH;

		return $this;
	}

	/**
	 * Get hasSSH
	 *
	 * @return boolean
	 */
	public function getHasSSH() {
		return $this->hasSSH;
	}

	/**
	 * Set gid
	 *
	 * @param integer $gid
	 *
	 * @return Domain
	 */
	public function setGid($gid) {
		$this->gid = $gid;

		return $this;
	}

	/**
	 * Get gid
	 *
	 * @return integer
	 */
	public function getGid() {
		return $this->gid;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Domain
	 */
	public function setPath($path) {
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
}
