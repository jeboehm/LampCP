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
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MailForward
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"mailaddress", "target"})
 */
class MailForward {
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="mailforward")
	 */
	private $domain;

	/**
	 * @var MailAddress
	 * @Assert\NotNull()
	 * @ManyToOne(targetEntity="MailAddress", inversedBy="mailforward")
	 */
	private $mailaddress;

	/**
	 * @var string
	 * @Assert\Email()
	 * @Assert\NotBlank()
	 * @ORM\Column(name="target", type="string", length=255)
	 */
	private $target;

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
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return MailForward
	 */
	public function setDomain($domain) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Get domain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set mailaddress
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\MailAddress $mailaddress
	 *
	 * @return MailForward
	 */
	public function setMailaddress($mailaddress) {
		$this->mailaddress = $mailaddress;

		return $this;
	}

	/**
	 * Get mailaddress
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\MailAddress
	 */
	public function getMailaddress() {
		return $this->mailaddress;
	}

	/**
	 * Set target
	 *
	 * @param string $target
	 *
	 * @return MailForward
	 */
	public function setTarget($target) {
		$this->target = $target;

		return $this;
	}

	/**
	 * Get target
	 *
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}
}
