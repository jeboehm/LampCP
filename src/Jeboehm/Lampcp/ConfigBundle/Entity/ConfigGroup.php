<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class ConfigGroup {
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
	 * @ORM\Column(name="name", type="string", length=100)
	 */
	private $name;

	/**
	 * @var ConfigEntity[]
	 * @OneToMany(targetEntity="ConfigEntity", mappedBy="configgroup", cascade={"remove"})
	 */
	private $configentity;

	/**
	 * @return ConfigEntity[]
	 */
	public function getConfigentity() {
		return $this->configentity;
	}

	/**
	 * @param int $id
	 *
	 * @return ConfigGroup
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $name
	 *
	 * @return ConfigGroup
	 */
	public function setName($name) {
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
}
