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
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntityRepository")
 * @UniqueEntity(fields = {"name", "configgroup"})
 */
class ConfigEntity extends AbstractEntity {
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
     * @ORM\ManyToOne(targetEntity="ConfigGroup", inversedBy="configentity")
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
     * Set ConfigGroup
     *
     * @param ConfigGroup $configgroup
     *
     * @return ConfigEntity
     */
    public function setConfiggroup(ConfigGroup $configgroup) {
        $this->configgroup = $configgroup;

        return $this;
    }

    /**
     * Get ConfigGroup
     *
     * @return ConfigGroup
     */
    public function getConfiggroup() {
        return $this->configgroup;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return ConfigEntity
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ConfigEntity
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
     * Set type
     *
     * @param int $type
     *
     * @return ConfigEntity
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ConfigEntity
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
}
