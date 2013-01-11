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
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntityRepository")
 * @UniqueEntity(fields = {"name", "configgroup"})
 */
class ConfigEntity {
	const TYPE_INTEGER  = 0;
	const TYPE_STRING   = 1;
	const TYPE_PASSWORD = 2;
	const TYPE_BOOL     = 3;

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
	 * @var ConfigGroup
	 * @ORM\OrderBy({"name" = "asc"})
	 * @ManyToOne(targetEntity="ConfigGroup", inversedBy="configentity")
	 */
	private $configgroup;

	/**
	 * @var int
	 * @ORM\Column(name="type", type="integer")
	 */
	private $type;

	/**
	 * @var string
	 * @Assert\NotBlank()
	 * @ORM\Column(name="value", type="string", length=255)
	 */
	private $value;

	/**
	 * @param \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup $configgroup
	 *
	 * @return ConfigEntity
	 */
	public function setConfiggroup($configgroup) {
		$this->configgroup = $configgroup;

		return $this;
	}

	/**
	 * @return \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup
	 */
	public function getConfiggroup() {
		return $this->configgroup;
	}

	/**
	 * @param int $id
	 *
	 * @return ConfigEntity
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
	 * @return ConfigEntity
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param int $type
	 *
	 * @return ConfigEntity
	 */
	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $value
	 *
	 * @return ConfigEntity
	 */
	public function setValue($value) {
		$this->value = strval($value);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}
}
