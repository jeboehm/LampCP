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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Cron
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"name"})
 */
class Cron extends AbstractEntity {
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
	 * @Assert\NotBlank()
	 * @ORM\Column(name="name", type="string", length=64)
	 */
	private $name;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="lastrun", type="datetime")
	 */
	private $lastrun;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Cron
	 */
	public function setName($name) {
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set last run
	 *
	 * @param \DateTime $lastrun
	 *
	 * @return Cron
	 */
	public function setLastrun(\DateTime $lastrun) {
		$this->lastrun = $lastrun;

		return $this;
	}

	/**
	 * Get last run
	 *
	 * @return \DateTime
	 */
	public function getLastrun() {
		return $this->lastrun;
	}
}
