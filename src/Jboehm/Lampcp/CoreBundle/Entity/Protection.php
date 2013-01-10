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
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Protection
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("path")
 */
class Protection {
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
	 * @ManyToOne(targetEntity="Domain", inversedBy="protection")
	 */
	private $domain;

	/**
	 * @var ProtectionUser[]
	 * @OneToMany(targetEntity="ProtectionUser", mappedBy="protection", cascade={"remove"})
	 */
	private $protectionuser;

	/**
	 * @var string
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	private $path;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="realm", type="string", length=15)
	 */
	private $realm;

	/**
	 * Konstruktor
	 *
	 * @param Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
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
	 * @return Protection
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
	 * Set realm
	 *
	 * @param string $realm
	 *
	 * @return Protection
	 */
	public function setRealm($realm) {
		$this->realm = $realm;

		return $this;
	}

	/**
	 * Get realm
	 *
	 * @return string
	 */
	public function getRealm() {
		return $this->realm;
	}

	/**
	 * Set ProtectionUser
	 *
	 * @param array $protectionuser
	 *
	 * @return Protection
	 */
	public function setProtectionuser(array $protectionuser) {
		$this->protectionuser = $protectionuser;

		return $this;
	}

	/**
	 * Get ProtectionUser
	 *
	 * @return ProtectionUser[]
	 */
	public function getProtectionuser() {
		return $this->protectionuser;
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
}
