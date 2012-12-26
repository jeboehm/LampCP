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
 * Domain
 *
 * @ORM\Table()
 * @ORM\Entity
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
	 * @Assert\NotBlank()
	 * @ORM\Column(name="domain", type="string", length=100)
	 */
	private $domain;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasWeb", type="boolean")
	 */
	private $hasWeb;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasMail", type="boolean")
	 */
	private $hasMail;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasSSH", type="boolean")
	 */
	private $hasSSH;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->hasSSH  = false;
		$this->hasMail = false;
		$this->hasWeb  = false;
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
		$this->domain = $domain;

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
	 * Set hasWeb
	 *
	 * @param boolean $hasWeb
	 *
	 * @return Domain
	 */
	public function setHasWeb($hasWeb) {
		$this->hasWeb = $hasWeb;

		return $this;
	}

	/**
	 * Get hasWeb
	 *
	 * @return boolean
	 */
	public function getHasWeb() {
		return $this->hasWeb;
	}

	/**
	 * Set hasMail
	 *
	 * @param boolean $hasMail
	 *
	 * @return Domain
	 */
	public function setHasMail($hasMail) {
		$this->hasMail = $hasMail;

		return $this;
	}

	/**
	 * Get hasMail
	 *
	 * @return boolean
	 */
	public function getHasMail() {
		return $this->hasMail;
	}

	/**
	 * Set hasSSH
	 *
	 * @param boolean $hasSSH
	 *
	 * @return Domain
	 */
	public function setHasSSH($hasSSH) {
		$this->hasSSH = $hasSSH;

		return $this;
	}

	/**
	 * Get hasSSH
	 *
	 * @return boolean
	 */
	public function getHasSSH() {
		return $this->hasSSH;
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
}
