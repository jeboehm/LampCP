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
 * ProtectionUser
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"username", "protection"})
 */
class ProtectionUser extends AbstractEntity {
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
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="protectionuser")
     */
    private $domain;

    /**
     * @var Protection
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Protection", inversedBy="protectionuser")
     */
    private $protection;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-z\d-.]{1,255}$/i")
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\Length(min="6", max="50")
     */
    private $password;

    /**
     * Konstruktor
     *
     * @param Domain     $domain
     * @param Protection $protection
     */
    public function __construct(Domain $domain, Protection $protection) {
        $this->domain     = $domain;
        $this->protection = $protection;
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
     * Set password
     *
     * @param string $password
     *
     * @return ProtectionUser
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set Protection
     *
     * @param Protection $protection
     *
     * @return ProtectionUser
     */
    public function setProtection(Protection $protection) {
        $this->protection = $protection;

        return $this;
    }

    /**
     * Get Protection
     *
     * @return \Jeboehm\Lampcp\CoreBundle\Entity\Protection
     */
    public function getProtection() {
        return $this->protection;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return ProtectionUser
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
}
