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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Config
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Config {
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
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="value", type="string", length=255)
	 */
	private $value;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="changed", type="datetime")
	 */
	private $changed;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->changed = new \DateTime();
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
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Config
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
	 * Set value
	 *
	 * @param string $value
	 *
	 * @return Config
	 */
	public function setValue($value) {
		$this->value = strval($value);

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set changed
	 *
	 * @param \DateTime $changed
	 *
	 * @return Config
	 */
	public function setChanged($changed) {
		$this->changed = $changed;

		return $this;
	}

	/**
	 * Get changed
	 *
	 * @return \DateTime
	 */
	public function getChanged() {
		return $this->changed;
	}
}
