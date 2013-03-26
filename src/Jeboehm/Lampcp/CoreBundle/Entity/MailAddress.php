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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MailAddress
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields = {"address", "domain"})
 */
class MailAddress extends AbstractEntity {
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
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="mailaddress")
     */
    private $domain;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-z\d-.]{1,255}$/i")
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var MailAccount
     * @ORM\OneToOne(targetEntity="MailAccount", mappedBy="mailaddress", cascade={"persist","remove"})
     */
    private $mailaccount;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="MailForward", mappedBy="mailaddress", cascade={"persist","remove"})
     */
    private $mailforward;

    /**
     * Konstruktor
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain) {
        $this->domain      = $domain;
        $this->mailforward = new ArrayCollection();
        $this->mailaccount = new MailAccount($domain, $this);
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
     * Set address
     *
     * @param string $address
     *
     * @return MailAddress
     */
    public function setAddress($address) {
        $this->address = strtolower($address);

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set mailaccount
     *
     * @param MailAccount $mailaccount
     *
     * @return MailAddress
     */
    public function setMailaccount($mailaccount) {
        $this->mailaccount = $mailaccount;

        return $this;
    }

    /**
     * Get mailaccount
     *
     * @return MailAccount
     */
    public function getMailaccount() {
        return $this->mailaccount;
    }

    /**
     * Set mailforward
     *
     * @param Collection $mailforward
     *
     * @return MailAddress
     */
    public function setMailforward(Collection $mailforward) {
        if (!is_null($mailforward)) {
            foreach ($mailforward as $forward) {
                /** @var $forward MailForward */
                $forward->setDomain($this->domain);
                $forward->setMailaddress($this);
            }
        }

        $this->mailforward = $mailforward;

        return $this;
    }

    /**
     * Get mailforward
     *
     * @return Collection
     */
    public function getMailforward() {
        return $this->mailforward;
    }

    /**
     * Get full address (user@domain.de)
     *
     * @return string
     */
    public function getFullAddress() {
        return $this->address . '@' . $this->domain->getDomain();
    }
}
