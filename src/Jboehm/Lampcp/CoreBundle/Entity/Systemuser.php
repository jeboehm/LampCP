<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Systemuser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Systemuser {
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
	 * @ORM\Column(name="name", type="string", length=32)
	 */
	private $name;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="uid", type="integer")
	 */
	private $uid;

	/**
	 * @var Domain
	 *
	 * @ManyToOne(targetEntity="Domain")
	 * @JoinColumn(name="domain_id", referencedColumnName="id")
	 */
	private $domain;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	private $password;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Systemuser
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set uid
	 *
	 * @param integer $uid
	 *
	 * @return Systemuser
	 */
	public function setUid($uid) {
		$this->uid = $uid;

		return $this;
	}

	/**
	 * Get uid
	 *
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * Set domain
	 *
	 * @param Domain $domain
	 *
	 * @return Systemuser
	 */
	public function setDomain(Domain $domain) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Get domain
	 *
	 * @return Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return Systemuser
	 */
	public function setPassword($password) {
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}
}
