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
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection\ZoneCollection;

/**
 * Dns
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"subdomain", "domain"})
 */
class Dns extends AbstractEntity {
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
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="dns")
     */
    private $domain;

    /**
     * @var string
     * @ORM\Column(name="subdomain", type="string", length=255)
     */
    private $subdomain;

    /**
     * @var ZoneCollection
     * @ORM\Column(name="zonecollection", type="object")
     */
    private $zonecollection;

    /**
     * Konstruktor
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain) {
        $this->domain         = $domain;
        $this->subdomain      = '';
        $this->zonecollection = new ZoneCollection();
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
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * Set subdomain
     *
     * @param string $subdomain
     *
     * @return Dns
     */
    public function setSubdomain($subdomain) {
        $this->subdomain = strval($subdomain);

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
     * Set zonecollection
     *
     * @param ZoneCollection $zonecollection
     *
     * @return Dns
     */
    public function setZonecollection(ZoneCollection $zonecollection) {
        $this->zonecollection = $zonecollection;

        return $this;
    }

    /**
     * Get zonecollection
     *
     * @return ZoneCollection
     */
    public function getZonecollection() {
        return $this->zonecollection;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin() {
        if (!empty($this->subdomain)) {
            return $this->subdomain . '.' . $this->domain->getDomain();
        } else {
            return $this->domain->getDomain();
        }
    }
}
