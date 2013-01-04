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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MailAddress
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"address", "domain"})
 */
class MailAddress {
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="mailaddress")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="address", type="string", length=255)
	 */
	private $address;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasCatchAll", type="boolean")
	 */
	private $hasCatchAll;

	/**
	 * @var MailAccount
	 * @Assert\NotNull()
	 * @ManyToOne(targetEntity="MailAccount", inversedBy="mailaddress")
	 */
	private $mailaccount;

	/**
	 * Konstruktor
	 *
	 * @param Domain      $domain
	 * @param MailAccount $account
	 */
	public function __construct(Domain $domain, MailAccount $account) {
		$this->domain      = $domain;
		$this->mailaccount = $account;
		$this->hasCatchAll = false;
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
	 * Set address
	 *
	 * @param string $address
	 *
	 * @return MailAddress
	 */
	public function setAddress($address) {
		$this->address = $address;

		return $this;
	}

	/**
	 * Get address
	 *
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * Set hasCatchAll
	 *
	 * @param boolean $hasCatchAll
	 *
	 * @return MailAddress
	 */
	public function setHasCatchAll($hasCatchAll) {
		$this->hasCatchAll = $hasCatchAll;

		if($hasCatchAll) {
			$this->address = '*';
		}

		return $this;
	}

	/**
	 * Get hasCatchAll
	 *
	 * @return boolean
	 */
	public function getHasCatchAll() {
		return $this->hasCatchAll;
	}

	/**
	 * Set mailaccount
	 *
	 * @param MailAccount $mailaccount
	 *
	 * @return MailAddress
	 */
	public function setMailaccount($mailaccount) {
		$this->mailaccount = $mailaccount;

		return $this;
	}

	/**
	 * Get mailaccount
	 *
	 * @return MailAccount
	 */
	public function getMailaccount() {
		return $this->mailaccount;
	}
}
