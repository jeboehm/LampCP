<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MysqlDatabase
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MysqlDatabase {
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
	 * @Assert\NotNull()
	 * @ManyToOne(targetEntity="Domain",cascade={"persist"})
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="name", type="string", length=64)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="comment", type="string", length=255)
	 */
	private $comment;

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
	 * Get domain
	 *
	 * @return Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return MysqlDatabase
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
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return MysqlDatabase
	 */
	public function setComment($comment) {
		$this->comment = $comment;

		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return MysqlDatabase
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
