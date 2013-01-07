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
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
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
	 * @ManyToOne(targetEntity="User", inversedBy="domain")
	 */
	private $user;

	/**
	 * @var IpAddress[]
	 * @ManyToMany(targetEntity="IpAddress")
	 */
	private $ipaddress;

	/**
	 * @var string
	 * @ORM\Column(name="customconfig", type="text")
	 */
	private $customconfig;

	/**
	 * @var MailAccount[]
	 * @OneToMany(targetEntity="MailAccount", mappedBy="domain", cascade={"remove"})
	 */
	private $mailaccount;

	/**
	 * @var MailAddress[]
	 * @OneToMany(targetEntity="MailAddress", mappedBy="domain", cascade={"remove"})
	 */
	private $mailaddress;

	/**
	 * @var MysqlDatabase[]
	 * @OneToMany(targetEntity="MysqlDatabase", mappedBy="domain", cascade={"remove"})
	 */
	private $mysqldatabase;

	/**
	 * @var PathOption[]
	 * @OneToMany(targetEntity="PathOption", mappedBy="domain", cascade={"remove"})
	 */
	private $pathoption;

	/**
	 * @var Protection[]
	 * @OneToMany(targetEntity="Protection", mappedBy="domain", cascade={"remove"})
	 */
	private $protection;

	/**
	 * @var Subdomain[]
	 * @OneToMany(targetEntity="Subdomain", mappedBy="domain", cascade={"remove"})
	 */
	private $subdomain;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->customconfig = '';
		$this->webroot      = 'htdocs';
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
	 * Set ipaddresses
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\IpAddress[] $ipaddress
	 *
	 * @return IpAddress[]
	 */
	public function setIpaddress($ipaddress) {
		$this->ipaddress = $ipaddress;

		return $this;
	}

	/**
	 * Get ipaddresses
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\IpAddress[]
	 */
	public function getIpaddress() {
		return $this->ipaddress;
	}

	/**
	 * Set mailaccounts
	 *
	 * @param array $mailaccount
	 *
	 * @return Domain
	 */
	public function setMailaccount($mailaccount) {
		$this->mailaccount = $mailaccount;

		return $this;
	}

	/**
	 * Get mailaccounts
	 *
	 * @return MailAccount[]
	 */
	public function getMailaccount() {
		return $this->mailaccount;
	}

	/**
	 * Set mailaddresses
	 *
	 * @param array $mailaddress
	 *
	 * @return Domain
	 */
	public function setMailaddress($mailaddress) {
		$this->mailaddress = $mailaddress;

		return $this;
	}

	/**
	 * Get mailaddresses
	 *
	 * @return MailAddress[]
	 */
	public function getMailaddress() {
		return $this->mailaddress;
	}

	/**
	 * Set mysqldatabases
	 *
	 * @param array $mysqldatabase
	 *
	 * @return Domain
	 */
	public function setMysqldatabase($mysqldatabase) {
		$this->mysqldatabase = $mysqldatabase;

		return $this;
	}

	/**
	 * Get mysqldatabases
	 *
	 * @return MysqlDatabase[]
	 */
	public function getMysqldatabase() {
		return $this->mysqldatabase;
	}

	/**
	 * Set pathoptions
	 *
	 * @param array $pathoption
	 *
	 * @return Domain
	 */
	public function setPathoption($pathoption) {
		$this->pathoption = $pathoption;

		return $this;
	}

	/**
	 * Get pathoptions
	 *
	 * @return PathOption[]
	 */
	public function getPathoption() {
		return $this->pathoption;
	}

	/**
	 * Set protections
	 *
	 * @param array $protection
	 *
	 * @return Domain
	 */
	public function setProtection($protection) {
		$this->protection = $protection;

		return $this;
	}

	/**
	 * Get protections
	 *
	 * @return Protection[]
	 */
	public function getProtection() {
		return $this->protection;
	}

	/**
	 * Set subdomains
	 *
	 * @param array $subdomain
	 *
	 * @return Domain
	 */
	public function setSubdomain($subdomain) {
		$this->subdomain = $subdomain;

		return $this;
	}

	/**
	 * Get subdomains
	 *
	 * @return Subdomain[]
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}
}
