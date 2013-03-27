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

/**
 * UpdateExecution
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UpdateExecution {
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float")
     */
    private $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="execution_time", type="datetime")
     */
    private $executionTime;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return UpdateExecution
     */
    public function setName($name) {
        $this->name = $name;

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
     * Set version
     *
     * @param float $version
     *
     * @return UpdateExecution
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return float
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set executionTime
     *
     * @param \DateTime $executionTime
     *
     * @return UpdateExecution
     */
    public function setExecutionTime($executionTime) {
        $this->executionTime = $executionTime;

        return $this;
    }

    /**
     * Get executionTime
     *
     * @return \DateTime
     */
    public function getExecutionTime() {
        return $this->executionTime;
    }
}
