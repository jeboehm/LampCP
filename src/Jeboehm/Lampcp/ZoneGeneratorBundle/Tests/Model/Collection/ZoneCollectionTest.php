<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\Collection;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection\ZoneCollection;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\A;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AAAA;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\NS;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA;

/**
 * Class ZoneCollectionTest
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\Collection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ZoneCollectionTest extends \PHPUnit_Framework_TestCase {
    /**
     * Checks, that the array keys are in row.
     */
    public function testKeysAreInRow() {
        // Test aufbauen
        $rr = array();
        $zc = new ZoneCollection();

        for ($i = 0; $i < 4; $i++) {
            $rr[] = $this->_getRandomResourceRecord();
            $zc->add($rr[$i]);
        }

        $zc->remove(3);

        // Testen
        for ($i = 0; $i < 3; $i++) {
            $this->assertNotNull($zc->get($i));
        }
    }

    /**
     * Get soa from zone collection.
     */
    public function testGetSoa() {
        $zc = new ZoneCollection();

        $zc
            ->add(new SOA())
            ->add(new A());

        $getbytype = $zc->getByType('SOA');

        $this->assertInstanceOf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA', $zc->getSoa());
        $this->assertInstanceOf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA', array_pop($getbytype));
    }

    /**
     * Test add.
     */
    public function testAdd() {
        $zc = new ZoneCollection();

        $zc->add(new SOA());

        $this->assertInstanceOf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA', $zc->getSoa());

        $zc->add(new AAAA());

        $this->assertInstanceOf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AAAA', $zc->last());
    }

    /**
     * Test add non ResourceRecord object.
     *
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testAddOther() {
        $zc = new ZoneCollection();

        $zc->add(new \stdClass());
    }

    /**
     * Get resource record by type.
     */
    public function testGetAAAA() {
        $zc = new ZoneCollection();

        $zc
            ->add(new SOA())
            ->add(new A())
            ->add(new NS())
            ->add(new NS())
            ->add(new AAAA())
            ->add(new AAAA())
            ->add(new A());

        $return = $zc->getByType('AAAA');

        $this->assertTrue(is_array($return));
        $this->assertInstanceOf('Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AAAA', $return[0]);
    }

    /**
     * Get random resource record.
     *
     * @return AAAA
     */
    protected function _getRandomResourceRecord() {
        $rr = new AAAA();
        $rr->setName(rand(0, 99999));

        return $rr;
    }
}
