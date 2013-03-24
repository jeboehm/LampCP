<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form\Model;

use Jeboehm\Lampcp\CoreBundle\Entity\ConfigGroup;

class ConfigEntity {
    /** @var string */
    private $name;

    /** @var ConfigGroup */
    private $configgroup;

    /** @var int */
    private $type;

    /** @var string */
    private $value;

    /**
     * Set Value
     *
     * @param string $value
     *
     * @return ConfigEntity
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set Type
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
     * Get Type
     *
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return ConfigEntity
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set Configgroup
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\ConfigGroup $configgroup
     *
     * @return ConfigEntity
     */
    public function setConfiggroup(ConfigGroup $configgroup) {
        $this->configgroup = $configgroup;

        return $this;
    }

    /**
     * Get Configgroup
     *
     * @return \Jeboehm\Lampcp\CoreBundle\Entity\ConfigGroup
     */
    public function getConfiggroup() {
        return $this->configgroup;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName() {
        return sprintf('%s.%s', $this
            ->getConfiggroup()
            ->getName(), $this->getName());
    }
}