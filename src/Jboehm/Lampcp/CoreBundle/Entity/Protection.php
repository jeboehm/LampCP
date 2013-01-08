<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Protection
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"username", "domain"})
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="protection")
	 */
	private $domain;

	/**
	 * @var string
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
	 * @Assert\MinLength(6)
	 * @Assert\MaxLength(50)
	 */
	private $password;

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
		$this->path   = '';
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
		$this->path = strval($path);

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
		$this->username = strtolower($username);

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
	 * Get full path
	 *
	 * @return string
	 */
	public function getFullPath() {
		$path = $this->getDomain()->getPath();

		if(!empty($this->path)) {
			$path .= '/' . $this->getPath();
		}

		return $path;
	}
}
