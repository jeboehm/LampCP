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

use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer\FqdnTransformer;

/**
 * Class DnsRepository
 *
 * Provides methods for finding entities
 * by FQDN.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Entity
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DnsRepository extends EntityRepository {
    /**
     * Find DNS entity by FQDN.
     *
     * @param string $fqdn
     *
     * @return array
     */
    public function findByFqdn($fqdn) {
        $entities = $this->findAll();
        $arr      = array();

        foreach ($entities as $entity) {
            /** @var $entity Dns */
            $collection = $entity->getZonecollection();
            $records    = $collection->getValues();

            foreach ($records as $record) {
                /** @var $record AbstractResourceRecord */
                $recordFqdn = FqdnTransformer::getFqdn($entity, $record);

                if ($fqdn == $recordFqdn) {
                    $arr[] = $entity;
                }
            }
        }

        return $arr;
    }
}
