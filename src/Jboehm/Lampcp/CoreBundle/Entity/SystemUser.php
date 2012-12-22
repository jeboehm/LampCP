<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SystemUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SystemUser {
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
	 * @Assert\NotBlank()
	 * @ORM\Column(name="name", type="string", length=32)
	 */
	private $name;

	/**
	 * @var integer
	 * @Assert\Min(limit = "10000")
	 * @ORM\Column(name="uid", type="integer")
	 */
	private $uid;

	/**
	 * @var Domain
	 * @Assert\NotNull()
	 * @ManyToOne(targetEntity="Domain")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	private $password;

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

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
	 * @return SystemUser
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
	 * @return SystemUser
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
	 * @return SystemUser
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
