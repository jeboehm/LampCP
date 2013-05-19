<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model;

use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ZoneDefinition;

/**
 * Class ZoneDefinitionTest
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ZoneDefinitionTest extends \PHPUnit_Framework_TestCase {
    /**
     * Test create().
     */
    public function testCreate() {
        $expect = <<<EOT
zone "test.name" IN {
    type master;
    file "test.file";
};
EOT;

        $result = ZoneDefinition::create('test.name', 'test.file');
        $this->assertEquals($expect, $result);
    }
}
