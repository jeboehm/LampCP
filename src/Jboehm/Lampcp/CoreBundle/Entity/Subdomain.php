<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subdomain
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Subdomain {
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
	 * @JoinColumn(name="domain_id", referencedColumnName="id")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="subdomain", type="string", length=255)
	 */
	private $subdomain;

	/**
	 * @var string
	 * @Assert\NotBlank()
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
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
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
	 * Set subdomain
	 *
	 * @param string $subdomain
	 *
	 * @return Subdomain
	 */
	public function setSubdomain($subdomain) {
		$this->subdomain = $subdomain;

		return $this;
	}

	/**
	 * Get subdomain
	 *
	 * @return string
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Subdomain
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
