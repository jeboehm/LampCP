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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Jeboehm\Lampcp\CoreBundle\Utilities\FilesizeUtility;

/**
 * MailAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jeboehm\Lampcp\CoreBundle\Entity\MailAccountRepository")
 * @UniqueEntity(fields = {"username"})
 */
class MailAccount {
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="mailaccount")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @Assert\Regex("/^[a-z\d-.]{2,64}$/i")
	 * @ORM\Column(name="username", type="string", length=32)
	 */
	private $username;

	/**
	 * @var string
	 * @ORM\Column(name="password", type="string", length=255)
	 * @Assert\MinLength(6)
	 */
	private $password;

	/**
	 * @var integer
	 * @Assert\Min(limit = "1024")
	 * @ORM\Column(name="quota", type="integer")
	 */
	private $quota;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="enabled", type="boolean")
	 */
	private $enabled;

	/**
	 * @var MailAddress[]
	 *
	 * @OneToMany(targetEntity="MailAddress", mappedBy="mailaccount", cascade={"remove"})
	 */
	private $mailaddress;

	/**
	 * Konstruktor
	 */
	public function __construct(Domain $domain) {
		$this->enabled = true;
		$this->domain  = $domain;
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
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return MailAccount
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
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return MailAccount
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
	 * Set quota
	 *
	 * @param integer $quota
	 *
	 * @return MailAccount
	 */
	public function setQuota($quota) {
		$this->quota = $quota;

		return $this;
	}

	/**
	 * Get quota
	 *
	 * @return integer
	 */
	public function getQuota() {
		return $this->quota;
	}

	/**
	 * Get quota in a human readable format
	 *
	 * @return string
	 */
	public function getQuotaHumanReadable() {
		return FilesizeUtility::size_readable($this->quota);
	}

	/**
	 * Set enabled
	 *
	 * @param boolean $enabled
	 *
	 * @return MailAccount
	 */
	public function setEnabled($enabled) {
		$this->enabled = $enabled;

		return $this;
	}

	/**
	 * Get enabled
	 *
	 * @return boolean
	 */
	public function getEnabled() {
		return $this->enabled;
	}

	/**
	 * @return MailAddress[]
	 */
	public function getMailaddress() {
		return $this->mailaddress;
	}
}
