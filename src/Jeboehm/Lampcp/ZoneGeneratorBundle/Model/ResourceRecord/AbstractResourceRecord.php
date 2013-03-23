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

abstract class AbstractResourceRecord implements IResourceRecord {
    /** @var string */
    private $_name;

    /** @var integer */
    private $_ttl;

    /** @var string */
    private $_rdata;

    /**
     * Constructor
     */
    public function __construct() {
        $this->setTtl(1800)->setName('@');
    }

    /**
     * Get domain name
     *
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Set domain name
     *
     * @param string $name
     *
     * @return IResourceRecord
     */
    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get TTL (time to live)
     *
     * @return integer
     */
    public function getTtl() {
        return $this->_ttl;
    }

    /**
     * Set TTL (time to live)
     *
     * @param integer $ttl
     *
     * @return IResourceRecord
     */
    public function setTtl($ttl) {
        $this->_ttl = $ttl;

        return $this;
    }

    /**
     * Get resource data
     *
     * @return string
     */
    public function getRdata() {
        return $this->_rdata;
    }

    /**
     * Set resource data
     *
     * @param string $rdata
     *
     * @return IResourceRecord
     */
    public function setRdata($rdata) {
        $this->_rdata = $rdata;

        return $this;
    }

    /**
     * Get resource records type
     *
     * @return string
     */
    public function getType() {
        $class = explode('\\', $this->_getClassName());

        return end($class);
    }

    /**
     * Get class name
     *
     * @return string
     */
    abstract protected function _getClassName();
}
