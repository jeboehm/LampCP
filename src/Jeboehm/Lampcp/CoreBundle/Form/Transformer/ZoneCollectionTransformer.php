<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form\Transformer;

use Jeboehm\Lampcp\CoreBundle\Form\Model\DnsResourceModel;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection\ZoneCollection;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\DnsResourceRecordTypes;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ZoneCollectionTransformer
 *
 * Transforms a ZoneCollection to an array
 * of DnsResourceModels and vise versa
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ZoneCollectionTransformer implements DataTransformerInterface {
    /** @var ZoneCollection */
    private $_zone;

    /**
     * Constructor
     *
     * @param ZoneCollection $zone
     */
    public function __construct(ZoneCollection $zone) {
        $this->_zone = $zone;
    }

    /**
     * Transform ZoneCollection to DnsResourceModel Array
     *
     * @param ZoneCollection $value
     *
     * @return array
     */
    public function transform($value) {
        if ($value instanceof ZoneCollection) {
            $arr      = array();
            $position = 0;

            foreach ($value->getValues() as $rr) {
                /** @var $rr AbstractResourceRecord */
                $dnsResourceModel = new DnsResourceModel();
                $dnsResourceModel
                    ->setName($rr->getName())
                    ->setTtl($rr->getTtl())
                    ->setType($rr->getType())
                    ->setRdata($rr->getRdata())
                    ->setPosition($position);

                $arr[] = $dnsResourceModel;
                $position++;
            }

            return $arr;
        } else {
            return $value;
        }
    }

    /**
     * Add DnsResourceModel[] to Zone and return it
     *
     * @param DnsResourceModel[] $value
     *
     * @return ZoneCollection
     */
    public function reverseTransform($value) {
        if (is_array($value)) {
            $records = array();
            $zone    = $this->_zone;
            $zone->clear();

            foreach ($value as $rr) {
                /** @var $rr DnsResourceModel */
                /** @var $record AbstractResourceRecord */
                if (in_array($rr->getType(), DnsResourceRecordTypes::$types)) {
                    $class  = sprintf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\%s', $rr->getType());
                    $record = new $class;
                    $record
                        ->setName($rr->getName())
                        ->setTtl($rr->getTtl())
                        ->setRdata($rr->getRdata());

                    $pos = $rr->getPosition();

                    while (array_key_exists($pos, $records)) {
                        $pos++;
                    }

                    $records[$pos] = $record;
                }
            }

            ksort($records);

            foreach ($records as $record) {
                /** @var $record AbstractResourceRecord */
                $zone->add($record);
            }

            return $zone;
        } else {
            return $value;
        }
    }
}
