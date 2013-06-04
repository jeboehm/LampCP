<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Model;

/**
 * Class OpensslCertificate
 *
 * The oop way for getting information from openssl_x509_parse().
 *
 * @package Jeboehm\Lampcp\CoreBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class OpensslCertificate
{
    /** @var array */
    private $parsed;

    /**
     * Constructor.
     *
     * @param array $parsed openssl_x509_parse() return.
     */
    public function __construct(array $parsed)
    {
        $this->parsed = $parsed;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->parsed['name'];
    }

    /**
     * Get hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->parsed['hash'];
    }

    /**
     * Get version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->parsed['version'];
    }

    /**
     * Get serial number.
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->parsed['serialNumber'];
    }

    /**
     * Get valid from.
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        $date = new \DateTime();
        $date->setTimestamp($this->parsed['validFrom_time_t']);

        return $date;
    }

    /**
     * Get valid to.
     *
     * @return \DateTime
     */
    public function getValidTo()
    {
        $date = new \DateTime();
        $date->setTimestamp($this->parsed['validTo_time_t']);

        return $date;
    }
}
