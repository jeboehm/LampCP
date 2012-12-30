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

/**
 * Log
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Log {
	const TYPE_DEBUG = 0;
	const TYPE_INFO  = 1;
	const TYPE_WARN  = 2;
	const TYPE_ERROR = 3;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->time = new \DateTime('now');
	}

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="time", type="datetime")
	 */
	private $time;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="type", type="smallint")
	 */
	private $type;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="message", type="text")
	 */
	private $message;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set time
	 *
	 * @param \DateTime $time
	 *
	 * @return Log
	 */
	public function setTime($time) {
		$this->time = $time;

		return $this;
	}

	/**
	 * Get time
	 *
	 * @return \DateTime
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * Set type
	 *
	 * @param integer $type
	 *
	 * @return Log
	 */
	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set message
	 *
	 * @param string $message
	 *
	 * @return Log
	 */
	public function setMessage($message) {
		$this->message = $message;

		return $this;
	}

	/**
	 * Get message
	 *
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
}
