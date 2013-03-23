<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord;

interface IResourceRecord {
    /**
     * Get domain name
     *
     * @return string
     */
    public function getName();

    /**
     * Set domain name
     *
     * @param string $name
     *
     * @return IResourceRecord
     */
    public function setName($name);

    /**
     * Get TTL (time to live)
     *
     * @return integer
     */
    public function getTtl();

    /**
     * Set TTL (time to live)
     *
     * @param integer $ttl
     *
     * @return IResourceRecord
     */
    public function setTtl($ttl);

    /**
     * Get resource data
     *
     * @return string
     */
    public function getRdata();

    /**
     * Set resource data
     *
     * @param string $rdata
     *
     * @return IResourceRecord
     */
    public function setRdata($rdata);

    /**
     * Get resource records type
     *
     * @return string
     */
    public function getType();
}
