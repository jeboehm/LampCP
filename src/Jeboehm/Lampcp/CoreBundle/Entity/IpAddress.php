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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * IpAddress
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class IpAddress extends AbstractEntity {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Domain", mappedBy="ipaddress")
     */
    private $domain;

    /**
     * @var string
     * @ORM\Column(name="alias", type="string", length=30)
     */
    private $alias;

    /**
     * @var string
     * @Assert\Ip(version="all")
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var int
     * @Assert\Min(1)
     * @Assert\Max(65535)
     * @ORM\Column(name="port", type="integer")
     */
    private $port;

    /**
     * @var bool
     * @ORM\Column(name="hasSsl", type="boolean")
     */
    private $hasSsl;

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
     * Set alias
     *
     * @param string $alias
     *
     * @return IpAddress
     */
    public function setAlias($alias) {
        $this->alias = strval($alias);

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return IpAddress
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * @param int $port
     *
     * @return IpAddress
     */
    public function setPort($port) {
        $this->port = $port;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set hasSsl
     *
     * @param boolean $hasSsl
     *
     * @return IpAddress
     */
    public function setHasSsl($hasSsl) {
        $this->hasSsl = $hasSsl;

        return $this;
    }

    /**
     * Get hasSsl
     *
     * @return boolean
     */
    public function getHasSsl() {
        return $this->hasSsl;
    }

    /**
     * Return true, if this is ipv6
     *
     * @return bool
     */
    public function isIpv6() {
        if (filter_var($this->getIp(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }

        return true;
    }

    /**
     * Set domain
     *
     * @param Collection $domain
     *
     * @return IpAddress
     */
    public function setDomain(Collection $domain) {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return Collection
     */
    public function getDomain() {
        return $this->domain;
    }
}
