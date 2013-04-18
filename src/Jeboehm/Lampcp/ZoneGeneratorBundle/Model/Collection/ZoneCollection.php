<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Class ZoneCollection
 *
 * Collects Resource Records.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ZoneCollection extends ArrayCollection {
    /** @var SOA */
    protected $_soa;

    /**
     * Add record to collection
     *
     * @param AbstractResourceRecord $value
     *
     * @return ZoneCollection
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function add($value) {
        if ($value instanceof SOA) {
            $this->_soa = $value;
        } elseif ($value instanceof AbstractResourceRecord) {
            parent::add($value);
            $this->_sortKeys();
        } else {
            throw new UnexpectedTypeException($value, 'Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord');
        }

        return $this;
    }

    /**
     * Get SOA
     *
     * @return \Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA
     */
    public function getSoa() {
        return $this->_soa;
    }

    /**
     * Get resource records by type
     *
     * @param string $type
     *
     * @return AbstractResourceRecord[]
     */
    public function getByType($type) {
        $type = strtoupper($type);
        $arr  = array();

        if ($type == 'SOA') {
            $arr[] = $this->getSoa();
        } else {
            foreach ($this->getValues() as $rr) {
                /** @var $rr AbstractResourceRecord */
                if ($rr->getType() == $type) {
                    $arr[] = $rr;
                }
            }
        }

        return $arr;
    }

    /**
     * Sort the array keys.
     */
    protected function _sortKeys() {
        $values = $this->getValues();
        parent::__construct($values);
    }
}
