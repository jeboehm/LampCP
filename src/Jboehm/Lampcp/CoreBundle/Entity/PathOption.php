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

/**
 * PathOption
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PathOption {
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
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasDirectoryListing", type="boolean")
	 */
	private $hasDirectoryListing;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="error404", type="string", length=255)
	 */
	private $error404;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="error403", type="string", length=255)
	 */
	private $error403;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="error500", type="string", length=255)
	 */
	private $error500;

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->hasDirectoryListing = false;
		$this->error403            = '';
		$this->error404            = '';
		$this->error500            = '';
		$this->domain              = $domain;
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
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return PathOption
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
	 * Set hasDirectoryListing
	 *
	 * @param boolean $hasDirectoryListing
	 *
	 * @return PathOption
	 */
	public function setHasDirectoryListing($hasDirectoryListing) {
		$this->hasDirectoryListing = $hasDirectoryListing;

		return $this;
	}

	/**
	 * Get hasDirectoryListing
	 *
	 * @return boolean
	 */
	public function getHasDirectoryListing() {
		return $this->hasDirectoryListing;
	}

	/**
	 * Set page404
	 *
	 * @param string $page404
	 *
	 * @return PathOption
	 */
	public function setPage404($page404) {
		$this->page404 = $page404;

		return $this;
	}

	/**
	 * Get page404
	 *
	 * @return string
	 */
	public function getPage404() {
		return $this->page404;
	}

	/**
	 * Set error404
	 *
	 * @param string $error404
	 *
	 * @return PathOption
	 */
	public function setError404($error404) {
		$this->error404 = $error404;

		return $this;
	}

	/**
	 * Get error404
	 *
	 * @return string
	 */
	public function getError404() {
		return $this->error404;
	}

	/**
	 * Set error403
	 *
	 * @param string $error403
	 *
	 * @return PathOption
	 */
	public function setError403($error403) {
		$this->error403 = $error403;

		return $this;
	}

	/**
	 * Get error403
	 *
	 * @return string
	 */
	public function getError403() {
		return $this->error403;
	}

	/**
	 * Set error500
	 *
	 * @param string $error500
	 *
	 * @return PathOption
	 */
	public function setError500($error500) {
		$this->error500 = $error500;

		return $this;
	}

	/**
	 * Get error500
	 *
	 * @return string
	 */
	public function getError500() {
		return $this->error500;
	}
}
