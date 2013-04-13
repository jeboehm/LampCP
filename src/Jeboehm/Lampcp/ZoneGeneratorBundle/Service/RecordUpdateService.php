<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\CoreBundle\Entity\DnsRepository;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\Controller\HostNotValid;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer\FqdnTransformer;

/**
 * Class RecordUpdateService
 *
 * Find and update a 'A' or 'AAAA' record by FQDN.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class RecordUpdateService {
    /** @var EntityManager */
    protected $_em;

    /** @var array */
    static protected $_types = array('AAAA', 'A');

    /**
     * Constructor.
     *
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->_em = $em;
    }

    /**
     * Get repository.
     *
     * @return DnsRepository
     */
    protected function _getRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:Dns');
    }

    /**
     * Get Dns Entity by FQDN.
     *
     * @param string $fqdn
     *
     * @return Dns
     */
    protected function _getDnsByFqdn($fqdn) {
        $arr = $this
            ->_getRepository()
            ->findByFqdn($fqdn);

        if (count($arr) > 0) {
            /** @var $dns Dns */
            $dns = array_pop($arr);

            return $dns;
        }

        return null;
    }

    /**
     * Get resource record by FQDN.
     *
     * @param Dns    $dns
     * @param string $fqdn
     *
     * @return AbstractResourceRecord[]
     */
    protected function _getResourceRecordsByFqdn(Dns $dns, $fqdn) {
        $records    = array();
        $matRecords = array();

        // Collect records by type
        foreach (self::$_types as $type) {
            $records = array_merge($records, $dns
                ->getZonecollection()
                ->getByType($type));
        }

        // Collect matching records
        foreach ($records as $rr) {
            /** @var $rr AbstractResourceRecord */
            if (FqdnTransformer::getFqdn($dns, $rr) === $fqdn) {
                $matRecords[] = $rr;
            }
        }

        return $matRecords;
    }

    /**
     * Update resource record by FQDN.
     *
     * @param string $fqdn
     * @param string $ip
     *
     * @return bool
     * @throws HostNotValid
     */
    public function update($fqdn, $ip) {
        $dns           = $this->_getDnsByFqdn($fqdn);
        $recordUpdated = false;

        if ($dns) {
            $records = $this->_getResourceRecordsByFqdn($dns, $fqdn);
            $zone    = $dns->getZonecollection();

            foreach ($records as $record) {
                switch ($record->getType()) {
                    case 'A':
                        if (!$this->_isIpV6($ip)) {
                            $zone->removeElement($record);

                            $record->setRdata($ip);

                            $zone->add($record);
                            $recordUpdated = true;
                        }
                        break;

                    case 'AAAA':
                        if ($this->_isIpV6($ip)) {
                            $zone->removeElement($record);

                            $record->setRdata($ip);

                            $zone->add($record);
                            $recordUpdated = true;
                        }
                        break;
                }

                if ($recordUpdated) {
                    $zone
                        ->getSoa()
                        ->refreshSerial();

                    $dns->setZonecollection(clone $zone);
                }
            }
        }

        if ($recordUpdated) {
            $this->_em->flush();
        }

        return $recordUpdated;
    }

    /**
     * True, if $ip is IPv6.
     *
     * @param string $ip
     *
     * @return bool
     */
    protected function _isIpV6($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
}