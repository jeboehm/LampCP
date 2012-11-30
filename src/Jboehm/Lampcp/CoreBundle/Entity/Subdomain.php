<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

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
	 *
	 * @ManyToOne(targetEntity="Domain")
	 * @JoinColumn(name="domain_id", referencedColumnName="id")
	 */
	private $domain;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="subdomain", type="string", length=255)
	 */
	private $subdomain;

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
	 * @param Domain $domain
	 *
	 * @return Subdomain
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

		if(strlen($path) > 0) {
			$this->setIsRedirect(false);
		}

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
	 * Set isRedirect
	 *
	 * @param boolean $isRedirect
	 *
	 * @return Subdomain
	 */
	public function setIsRedirect($isRedirect) {
		$this->isRedirect = $isRedirect;

		if($isRedirect) {
			$this->setPath(null);
		} else {
			$this->setRedirectUrl(null);
		}

		return $this;
	}

	/**
	 * Get isRedirect
	 *
	 * @return boolean
	 */
	public function getIsRedirect() {
		return $this->isRedirect;
	}

	/**
	 * Set redirectUrl
	 *
	 * @param string $redirectUrl
	 *
	 * @return Subdomain
	 */
	public function setRedirectUrl($redirectUrl) {
		$this->redirectUrl = $redirectUrl;

		if(strlen($redirectUrl) > 0) {
			$this->setIsRedirect(true);
		}

		return $this;
	}

	/**
	 * Get redirectUrl
	 *
	 * @return string
	 */
	public function getRedirectUrl() {
		return $this->redirectUrl;
	}
}
