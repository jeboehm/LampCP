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
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Domain
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("domain")
 */
class Domain {
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
	 * @Assert\Regex("/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i")
	 * @Assert\NotBlank()
	 * @ORM\Column(name="domain", type="string", length=100)
	 */
	private $domain;

	/**
	 * @var Certificate
	 * @ORM\ManyToOne(targetEntity="Certificate", inversedBy="domain")
	 */
	private $certificate;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="webroot", type="string", length=255)
	 */
	private $webroot;

	/**
	 * @var User
	 * @Assert\NotNull()
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="domain")
	 */
	private $user;

	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="IpAddress", inversedBy="domain")
	 */
	private $ipaddress;

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
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="MailAccount", mappedBy="domain", cascade={"remove"})
	 */
	private $mailaccount;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="MailAddress", mappedBy="domain", cascade={"remove"})
	 */
	private $mailaddress;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="MailForward", mappedBy="domain", cascade={"remove"})
	 */
	private $mailforward;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="MysqlDatabase", mappedBy="domain", cascade={"remove"})
	 */
	private $mysqldatabase;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="PathOption", mappedBy="domain", cascade={"remove"})
	 */
	private $pathoption;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="Protection", mappedBy="domain", cascade={"remove"})
	 */
	private $protection;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="ProtectionUser", mappedBy="domain", cascade={"remove"})
	 */
	private $protectionuser;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="Subdomain", mappedBy="domain", cascade={"remove"})
	 */
	private $subdomain;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->customconfig   = '';
		$this->webroot        = 'htdocs';
		$this->parsePhp       = true;
		$this->subdomain      = new ArrayCollection();
		$this->protection     = new ArrayCollection();
		$this->protectionuser = new ArrayCollection();
		$this->pathoption     = new ArrayCollection();
		$this->mysqldatabase  = new ArrayCollection();
		$this->mailforward    = new ArrayCollection();
		$this->mailaddress    = new ArrayCollection();
		$this->mysqldatabase  = new ArrayCollection();
		$this->ipaddress      = new ArrayCollection();
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
	 * Set domain
	 *
	 * @param string $domain
	 *
	 * @return Domain
	 */
	public function setDomain($domain) {
		$this->domain = strtolower($domain);

		return $this;
	}

	/**
	 * Get domain
	 *
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set certificate
	 *
	 * @param Certificate $certificate
	 *
	 * @return Domain
	 */
	public function setCertificate($certificate) {
		$this->certificate = $certificate;

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
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Domain
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
	 * Set webroot
	 *
	 * @param string $webroot
	 *
	 * @return Domain
	 */
	public function setWebroot($webroot) {
		$this->webroot = $webroot;

		return $this;
	}

	/**
	 * Get webroot
	 *
	 * @return string
	 */
	public function getWebroot() {
		return $this->webroot;
	}

	/**
	 * Set parse php
	 *
	 * @param boolean $parsePhp
	 *
	 * @return Domain
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
	 * @return Domain
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
	 * Set user
	 *
	 * @param User $user
	 *
	 * @return Domain
	 */
	public function setUser($user) {
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Get full webroot path
	 *
	 * @return string
	 */
	public function getFullWebrootPath() {
		return $this->path . '/' . $this->webroot;
	}

	/**
	 * Set IpAddresses
	 *
	 * @param Collection $ipaddress
	 *
	 * @return Domain
	 */
	public function setIpaddress(Collection $ipaddress) {
		$this->ipaddress = $ipaddress;

		return $this;
	}

	/**
	 * Get IpAddresses
	 *
	 * @return Collection
	 */
	public function getIpaddress() {
		return $this->ipaddress;
	}

	/**
	 * Get mailaccounts
	 *
	 * @return Collection
	 */
	public function getMailaccount() {
		return $this->mailaccount;
	}

	/**
	 * Get mailforwards
	 *
	 * @return Collection
	 */
	public function getMailforward() {
		return $this->mailforward;
	}

	/**
	 * Get mailaddresses
	 *
	 * @return Collection
	 */
	public function getMailaddress() {
		return $this->mailaddress;
	}

	/**
	 * Get mysqldatabases
	 *
	 * @return Collection
	 */
	public function getMysqldatabase() {
		return $this->mysqldatabase;
	}

	/**
	 * Get pathoptions
	 *
	 * @return Collection
	 */
	public function getPathoption() {
		return $this->pathoption;
	}

	/**
	 * Get protections
	 *
	 * @return Collection
	 */
	public function getProtection() {
		return $this->protection;
	}

	/**
	 * Get protectionusers
	 *
	 * @return Collection
	 */
	public function getProtectionuser() {
		return $this->protectionuser;
	}

	/**
	 * Get subdomains
	 *
	 * @return Collection
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}
}
