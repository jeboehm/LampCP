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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends AbstractEntity {
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
     * @var string
     *
     * @ORM\Column(name="groupname", type="string", length=255)
     */
    private $groupname;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer")
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="gid", type="integer")
     */
    private $gid;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Domain", mappedBy="user")
     */
    private $domain;

    /**
     * Konstruktor
     */
    public function __construct() {
        $this->domain = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set uid
     *
     * @param integer $uid
     *
     * @return User
     */
    public function setUid($uid) {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * Set gid
     *
     * @param integer $gid
     *
     * @return User
     */
    public function setGid($gid) {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid
     *
     * @return integer
     */
    public function getGid() {
        return $this->gid;
    }

    /**
     * @param string $groupname
     *
     * @return User
     */
    public function setGroupname($groupname) {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupname() {
        return $this->groupname;
    }

    /**
     * @return Collection
     */
    public function getDomain() {
        return $this->domain;
    }
}
