<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Model;

use \ReflectionClass;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;

/**
 * Class VhostTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostTest extends \PHPUnit_Framework_TestCase {
    /**
     * Test vhost address generation.
     *
     * @param Vhost  $vhost
     * @param string $expect
     *
     * @dataProvider providerTestGetVhostAddress
     */
    public function testGetVhostAddress(Vhost $vhost, $expect) {
        $this->assertEquals($expect, $vhost->getVhostAddress());
    }

    /**
     * Dataprovider for testGetVhostAddress.
     *
     * @return array
     */
    public function providerTestGetVhostAddress() {
        $ip4    = new IpAddress();
        $vhost4 = new Vhost();
        $ip6    = new IpAddress();
        $vhost6 = new Vhost();

        $ip4
            ->setIp('127.0.0.1')
            ->setPort(80);
        $vhost4->setIpaddress($ip4);

        $ip6
            ->setIp('2001:db8::1428:57ab')
            ->setPort(80);
        $vhost6->setIpaddress($ip6);

        return array(
            array($vhost4, '127.0.0.1:80'),
            array($vhost6, '[2001:db8::1428:57ab]:80'),
        );
    }

    /**
     * Test 'is folder root or child' method.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $expect
     *
     * @dataProvider providerTestIsFolderRootOrChild
     */
    public function testIsFolderRootOrChild($haystack, $needle, $expect) {
        $vhost  = new Vhost();
        $method = $this->_getMethod('_isFolderRootOrChild');
        $this->assertEquals($expect, $method->invokeArgs($vhost, array($haystack, $needle)));
    }

    /**
     * Dataprovider for testIsFolderRootOrChild.
     *
     * @return array
     */
    public function providerTestIsFolderRootOrChild() {
        return array(
            array('/home/j/john', '/home/j/john/pictures', true),
            array('/home/j/john', '/tmp', false),
            array('/home/j/john', '/home/j/john', true),
        );
    }

    /**
     * Get protected / private method.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected static function _getMethod($name) {
        $class  = new ReflectionClass('Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
