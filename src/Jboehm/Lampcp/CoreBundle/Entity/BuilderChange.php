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
 * BuilderChange
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BuilderChange {
	const METHOD_UPDATE = 0;
	const METHOD_REMOVE = 1;
	const METHOD_CREATE = 2;

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
	 *
	 * @ORM\Column(name="entityname", type="string", length=50)
	 */
	private $entityname;

	/**
	 * @var int
	 * @ORM\Column(name="method", type="integer")
	 */
	private $method;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set entityname
	 *
	 * @param string $entityname
	 *
	 * @return BuilderChange
	 */
	public function setEntityname($entityname) {
		$this->entityname = $entityname;

		return $this;
	}

	/**
	 * Get entityname
	 *
	 * @return string
	 */
	public function getEntityname() {
		return $this->entityname;
	}

	/**
	 * Set method
	 *
	 * @param int $method
	 *
	 * @return BuilderChange
	 */
	public function setMethod($method) {
		$this->method = $method;

		return $this;
	}

	/**
	 * Get method
	 *
	 * @return int
	 */
	public function getMethod() {
		return $this->method;
	}
}
