<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Admin {
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
	 * @ORM\Column(name="email", type="string", length=255)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	private $password;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="registered", type="datetime")
	 */
	private $registered;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="lastseen", type="datetime")
	 */
	private $lastseen;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Admin
	 */
	public function setEmail($email) {
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return Admin
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
	 * Set registered
	 *
	 * @param \DateTime $registered
	 *
	 * @return Admin
	 */
	public function setRegistered($registered) {
		$this->registered = $registered;

		return $this;
	}

	/**
	 * Get registered
	 *
	 * @return \DateTime
	 */
	public function getRegistered() {
		return $this->registered;
	}

	/**
	 * Set lastseen
	 *
	 * @param \DateTime $lastseen
	 *
	 * @return Admin
	 */
	public function setLastseen($lastseen) {
		$this->lastseen = $lastseen;

		return $this;
	}

	/**
	 * Get lastseen
	 *
	 * @return \DateTime
	 */
	public function getLastseen() {
		return $this->lastseen;
	}
}
