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
 * Subdomain
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"subdomain", "domain"})
 */
class Subdomain {
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="subdomain")
	 */
	private $domain;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @Assert\Regex("/^[a-z\d-.]{1,255}$/i")
	 * @ORM\Column(name="subdomain", type="string", length=255)
	 */
	private $subdomain;

	/**
	 * @var string
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @ORM\Column(name="customconfig", type="text")
	 */
	private $customconfig;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain       = $domain;
		$this->path         = '';
		$this->customconfig = '';
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
	 * Set subdomain
	 *
	 * @param string $subdomain
	 *
	 * @return Subdomain
	 */
	public function setSubdomain($subdomain) {
		$this->subdomain = strtolower($subdomain);

		return $this;
	}

	/**
	 * Get subdomain
	 *
	 * @return string
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Subdomain
	 */
	public function setPath($path) {
		$this->path = strval($path);

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
	 * Get full domain (subdomain.domain.tld)
	 *
	 * @return string
	 */
	public function getFullDomain() {
		return $this->getSubdomain() . '.' . $this->getDomain()->getDomain();
	}

	/**
	 * Get full path
	 *
	 * @return string
	 */
	public function getFullPath() {
		$path = $this->getDomain()->getPath();

		if(!empty($this->path)) {
			$path .= '/' . $this->getPath();
		}

		return $path;
	}

	/**
	 * @param string $customconfig
	 *
	 * @return Subdomain
	 */
	public function setCustomconfig($customconfig) {
		$this->customconfig = strval($customconfig);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCustomconfig() {
		return $this->customconfig;
	}
}
