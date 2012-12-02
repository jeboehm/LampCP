<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MailAddress
 *
 * @ORM\Table()
 * @ORM\Entity
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
	 * @ManyToOne(targetEntity="Domain")
	 * @JoinColumn(name="domain_id", referencedColumnName="id")
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
	 * @ManyToOne(targetEntity="MailAccount")
	 * @JoinColumn(name="mailaccount_id", referencedColumnName="id")
	 */
	private $mailaccount;

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain      = $domain;
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
