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
use Symfony\Component\Validator\Constraints as Assert;
use Jeboehm\Lampcp\CoreBundle\Utilities\FilesizeUtility;

/**
 * MailAccount
 *
 * @ORM\Table()
 * @ORM\Entity
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
	 * @ORM\ManyToOne(targetEntity="Domain", inversedBy="mailaccount")
	 */
	private $domain;

	/**
	 * @var MailAddress
	 * @Assert\NotNull()
	 * @ORM\OneToOne(targetEntity="MailAddress", inversedBy="mailaccount")
	 */
	private $mailaddress;

	/**
	 * @var boolean
	 * @ORM\Column(name="enabled", type="boolean")
	 */
	private $enabled;

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
	 * Konstruktor
	 */
	public function __construct(Domain $domain, MailAddress $address) {
		$this->domain      = $domain;
		$this->mailaddress = $address;
		$this->quota       = 102400; // 100 MiB
		$this->password    = '';
		$this->enabled     = false;
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
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return MailAccount
	 */
	public function setPassword($password) {
		if(!empty($password)) {
			$this->password = md5(strval($password));
		}

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
	 * Get MailAddress
	 *
	 * @return MailAddress
	 */
	public function getMailaddress() {
		return $this->mailaddress;
	}
}
