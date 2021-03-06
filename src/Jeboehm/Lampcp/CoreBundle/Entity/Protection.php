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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Protection
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"path", "domain"})
 */
class Protection extends AbstractEntity
{
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
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="protection")
     */
    private $domain;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="ProtectionUser", mappedBy="protection", cascade={"remove"})
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
    public function __construct(Domain $domain)
    {
        $this->domain         = $domain;
        $this->protectionuser = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get realm
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set realm
     *
     * @param string $realm
     *
     * @return Protection
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Get ProtectionUser
     *
     * @return Collection
     */
    public function getProtectionuser()
    {
        return $this->protectionuser;
    }

    /**
     * Set ProtectionUser
     *
     * @param Collection $protectionuser
     *
     * @return $this
     */
    public function setProtectionuser(Collection $protectionuser)
    {
        $this->protectionuser = $protectionuser;
        return $this;
    }

    /**
     * Get full path
     *
     * @return string
     */
    public function getFullPath()
    {
        $path = $this
            ->getDomain()
            ->getPath();

        if (!empty($this->path)) {
            $path .= '/' . $this->getPath();
        }

        return $path;
    }

    /**
     * Get domain
     *
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Protection
     */
    public function setPath($path)
    {
        $this->path = strval($path);

        return $this;
    }
}
