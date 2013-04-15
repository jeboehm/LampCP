<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\ResourceRecord;

use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA;

/**
 * Class SOATest
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\ResourceRecord
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class SOATest extends \PHPUnit_Framework_TestCase {
    /**
     * Test serial refreshing on the same day.
     */
    public function testRefreshSerialSameDay() {
        $soa = new SOA();

        $this->assertEquals('01', substr($soa->getSerial(), -2, 2));

        $soa->refreshSerial();
        $soa->refreshSerial();

        $this->assertEquals('03', substr($soa->getSerial(), -2, 2));
    }

    /**
     * Test serial refreshing on different days.
     */
    public function testRefreshSerialDaychange() {
        $soa = new SOA();
        $soa->setSerial('2012050512');

        $soa->refreshSerial();

        $expect = date('Ymd') . '01';
        $this->assertEquals($expect, $soa->getSerial());
    }
}
