<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Protection
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Protection {
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
	 * @ManyToOne(targetEntity="Domain")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="realm", type="string", length=15)
	 */
	private $realm;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="username", type="string", length=15)
	 */
	private $username;

	/**
	 * @var string
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
	 * Get domain
	 *
	 * @return Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Protection
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

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return Protection
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
	 * Set realm
	 *
	 * @param string $realm
	 *
	 * @return Protection
	 */
	public function setRealm($realm) {
		$this->realm = $realm;

		return $this;
	}

	/**
	 * Get realm
	 *
	 * @return string
	 */
	public function getRealm() {
		return $this->realm;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return Protection
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
	 * Crypt password
	 *
	 * @param string $password
	 *
	 * @return string
	 */
	public function cryptPassword($password) {
		return crypt($password, base64_encode($password));
	}
}
