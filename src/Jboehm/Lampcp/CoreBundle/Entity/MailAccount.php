<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MailAccount
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MailAccount {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var Domain
	 * @Assert\NotNull()
	 * @ManyToOne(targetEntity="Domain",cascade={"persist"})
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="username", type="string", length=32)
	 */
	private $username;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	private $password;

	/**
	 * @var integer
	 * @Assert\Min(limit = "10000")
	 * @ORM\Column(name="uid", type="integer")
	 */
	private $uid;

	/**
	 * @var integer
	 * @Assert\Min(limit = "1024")
	 * @ORM\Column(name="quota", type="integer")
	 */
	private $quota;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasPop3", type="boolean")
	 */
	private $hasPop3;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasImap4", type="boolean")
	 */
	private $hasImap4;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasSmtp", type="boolean")
	 */
	private $hasSmtp;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="enabled", type="boolean")
	 */
	private $enabled;

	/**
	 * Konstruktor
	 */
	public function __construct(Domain $domain) {
		$this->enabled  = true;
		$this->hasImap4 = false;
		$this->hasPop3  = false;
		$this->hasSmtp  = false;
		$this->domain   = $domain;
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
	 * Get domain
	 *
	 * @return Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return MailAccount
	 */
	public function setUsername($username) {
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return MailAccount
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

	/**
	 * Set uid
	 *
	 * @param integer $uid
	 *
	 * @return MailAccount
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
	 * Set quota
	 *
	 * @param integer $quota
	 *
	 * @return MailAccount
	 */
	public function setQuota($quota) {
		$this->quota = $quota;

		return $this;
	}

	/**
	 * Get quota
	 *
	 * @return integer
	 */
	public function getQuota() {
		return $this->quota;
	}

	/**
	 * Set hasPop3
	 *
	 * @param boolean $hasPop3
	 *
	 * @return MailAccount
	 */
	public function setHasPop3($hasPop3) {
		$this->hasPop3 = $hasPop3;

		return $this;
	}

	/**
	 * Get hasPop3
	 *
	 * @return boolean
	 */
	public function getHasPop3() {
		return $this->hasPop3;
	}

	/**
	 * Set hasImap4
	 *
	 * @param boolean $hasImap4
	 *
	 * @return MailAccount
	 */
	public function setHasImap4($hasImap4) {
		$this->hasImap4 = $hasImap4;

		return $this;
	}

	/**
	 * Get hasImap4
	 *
	 * @return boolean
	 */
	public function getHasImap4() {
		return $this->hasImap4;
	}

	/**
	 * Set hasSmtp
	 *
	 * @param boolean $hasSmtp
	 *
	 * @return MailAccount
	 */
	public function setHasSmtp($hasSmtp) {
		$this->hasSmtp = $hasSmtp;

		return $this;
	}

	/**
	 * Get hasSmtp
	 *
	 * @return boolean
	 */
	public function getHasSmtp() {
		return $this->hasSmtp;
	}

	/**
	 * Set enabled
	 *
	 * @param boolean $enabled
	 *
	 * @return MailAccount
	 */
	public function setEnabled($enabled) {
		$this->enabled = $enabled;

		return $this;
	}

	/**
	 * Get enabled
	 *
	 * @return boolean
	 */
	public function getEnabled() {
		return $this->enabled;
	}
}
