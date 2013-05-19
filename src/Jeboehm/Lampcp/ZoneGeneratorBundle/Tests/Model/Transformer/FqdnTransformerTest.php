<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\Transformer;

use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AAAA;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer\FqdnTransformer;

/**
 * Class FqdnTransformerTest
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Tests\Model\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FqdnTransformerTest extends \PHPUnit_Framework_TestCase {
    /**
     * Test getFqdn.
     *
     * @dataProvider provider
     */
    public function testGetFqdn(Dns $dns, AbstractResourceRecord $rr, $expect) {
        $result = FqdnTransformer::getFqdn($dns, $rr);
        $this->assertEquals($expect, $result);
    }

    /**
     * Provide test data for testGetFqdn.
     *
     * @return array
     */
    public function provider() {
        return array(
            array($this->_getDns('test1'), $this->_getResourceRecord('test2'), 'test2.test1.lampcp.de'),
            array($this->_getDns(), $this->_getResourceRecord('*'), '*.lampcp.de'),
            array($this->_getDns(), $this->_getResourceRecord('@'), 'lampcp.de'),
            array($this->_getDns(), $this->_getResourceRecord('test.'), 'test'),
        );
    }

    /**
     * Get DNS.
     *
     * @param string $subdomain
     *
     * @return Dns
     */
    protected function _getDns($subdomain = '') {
        $domain = new Domain();
        $domain->setDomain('lampcp.de');
        $dns = new Dns($domain);

        if (!empty($subdomain)) {
            $dns->setSubdomain($subdomain);
        }

        return $dns;
    }

    /**
     * Get resource record.
     *
     * @param string $name
     *
     * @return AAAA
     */
    protected function _getResourceRecord($name) {
        $rr = new AAAA();
        $rr->setName($name);

        return $rr;
    }
}
