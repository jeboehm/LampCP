<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Admin
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class Admin implements UserInterface, \Serializable {
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
	 * @Assert\Email()
	 * @ORM\Column(name="email", type="string", length=255)
	 */
	private $email;

	/**
	 * @var string
	 * @ORM\Column(name="salt", type="string", length=32)
	 */
	private $salt;

	/**
	 * @var string
	 * @ORM\Column(name="password", type="string", length=40)
	 * @Assert\MinLength(6)
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
	 * @var array
	 *
	 * @ORM\Column(name="roles", type="array")
	 */
	private $roles;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->setLastseen(new \DateTime());
		$this->setRegistered(new \DateTime());
		$this->setSalt(md5(uniqid(null, true)));
		$this->setRoles(array('ROLE_ADMIN'));
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
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Admin
	 */
	public function setEmail($email) {
		$this->email = strtolower($email);

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
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->getEmail();
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
	 * Set salt
	 *
	 * @param string $salt
	 *
	 * @return Admin
	 */
	public function setSalt($salt) {
		$this->salt = $salt;

		return $this;
	}

	/**
	 * Get salt
	 *
	 * @return string
	 */
	public function getSalt() {
		return $this->salt;
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

	/**
	 * Get roles
	 *
	 * @return array
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * Set roles
	 *
	 * @param $roles
	 *
	 * @return Admin
	 */
	public function setRoles($roles) {
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials() {
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize() {
		return serialize(array(
							  $this->id,
						 ));
	}

	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized) {
		list (
			$this->id,
			) = unserialize($serialized);
	}
}
