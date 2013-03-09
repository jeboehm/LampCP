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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;

/**
 * Subdomain
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"subdomain", "domain"})
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
	 * @ORM\ManyToOne(targetEntity="Domain", inversedBy="subdomain")
	 */
	private $domain;

	/**
	 * @var Certificate
	 * @ORM\ManyToOne(targetEntity="Certificate", inversedBy="subdomain")
	 */
	private $certificate;

	/**
	 * @var boolean
	 * @ORM\Column(name="forceSsl", type="boolean")
	 */
	private $forceSsl;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @Assert\Regex("/^[a-z\d-.]{1,255}$/i")
	 * @ORM\Column(name="subdomain", type="string", length=255)
	 */
	private $subdomain;

	/**
	 * @var string
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @Assert\Url()
	 * @ORM\Column(name="redirectUrl", type="string", length=255)
	 */
	private $redirectUrl;

	/**
	 * @var boolean
	 * @ORM\Column(name="isWildcard", type="boolean")
	 */
	private $isWildcard;

	/**
	 * @var boolean
	 * @ORM\Column(name="parsePhp", type="boolean")
	 */
	private $parsePhp;

	/**
	 * @var string
	 * @ORM\Column(name="customconfig", type="text")
	 */
	private $customconfig;

	/**
	 * @var Subdomain
	 * @ORM\ManyToOne(targetEntity="Subdomain", inversedBy="children")
	 */
	private $parent;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="Subdomain", mappedBy="parent")
	 */
	private $children;

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
		$this->domain       = $domain;
		$this->path         = 'htdocs';
		$this->redirectUrl  = '';
		$this->parsePhp     = true;
		$this->isWildcard   = false;
		$this->forceSsl     = false;
		$this->customconfig = '';
		$this->children     = new ArrayCollection();
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
	 * Set subdomain
	 *
	 * @param string $subdomain
	 *
	 * @return Subdomain
	 */
	public function setSubdomain($subdomain) {
		$this->subdomain = strtolower($subdomain);

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
	 * Set certificate
	 *
	 * @param Certificate $certificate
	 *
	 * @return Subdomain
	 */
	public function setCertificate($certificate) {
		$this->certificate = $certificate;

		if(!$certificate) {
			$this->forceSsl = false;
		}

		return $this;
	}

	/**
	 * Get certificate
	 *
	 * @return Certificate
	 */
	public function getCertificate() {
		return $this->certificate;
	}

	/**
	 * Set force ssl
	 *
	 * @param boolean $forceSsl
	 *
	 * @return Subdomain
	 */
	public function setForceSsl($forceSsl) {
		if($this->certificate) {
			$this->forceSsl = $forceSsl;
		} else {
			$this->forceSsl = false;
		}

		return $this;
	}

	/**
	 * Get force ssl
	 *
	 * @return boolean
	 */
	public function getForceSsl() {
		return $this->forceSsl;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Subdomain
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
	 * Set redirect url
	 *
	 * @param string $redirectUrl
	 *
	 * @return Subdomain
	 */
	public function setRedirectUrl($redirectUrl) {
		$this->redirectUrl = strval($redirectUrl);

		return $this;
	}

	/**
	 * Get redirect url
	 *
	 * @return string
	 */
	public function getRedirectUrl() {
		return $this->redirectUrl;
	}

	/**
	 * Get full domain (subdomain.domain.tld)
	 *
	 * @return string
	 */
	public function getFullDomain() {
		return $this->getSubdomain() . '.' . $this->getDomain()->getDomain();
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

	/**
	 * Get set wildcard
	 *
	 * @param boolean $isWildcard
	 *
	 * @return Domain
	 */
	public function setIsWildcard($isWildcard) {
		$this->isWildcard = $isWildcard;

		return $this;
	}

	/**
	 * Get is wildcard
	 *
	 * @return boolean
	 */
	public function getIsWildcard() {
		return $this->isWildcard;
	}

	/**
	 * Set parse php
	 *
	 * @param boolean $parsePhp
	 *
	 * @return Subdomain
	 */
	public function setParsePhp($parsePhp) {
		$this->parsePhp = $parsePhp;

		return $this;
	}

	/**
	 * Get parse php
	 *
	 * @return boolean
	 */
	public function getParsePhp() {
		return $this->parsePhp;
	}

	/**
	 * Set customconfig
	 *
	 * @param string $customconfig
	 *
	 * @return Subdomain
	 */
	public function setCustomconfig($customconfig) {
		$this->customconfig = strval($customconfig);

		return $this;
	}

	/**
	 * Get customconfig
	 *
	 * @return string
	 */
	public function getCustomconfig() {
		return $this->customconfig;
	}

	/**
	 * Set children
	 *
	 * @param \Doctrine\Common\Collections\Collection $children
	 *
	 * @return Subdomain
	 */
	public function setChildren($children) {
		$this->children = $children;

		return $this;
	}

	/**
	 * Get children
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * Set parent subdomain
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain $parent
	 *
	 * @return Subdomain
	 */
	public function setParent($parent) {
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Get parent subdomain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain
	 */
	public function getParent() {
		return $this->parent;
	}
}
