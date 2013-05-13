<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Tests\Model\Validator;

use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\VhostValidator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VhostValidatorTest
 *
 * @package Jeboehm\Lampcp\SetupBundle\Tests\Model\Validator
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostValidatorTest extends WebTestCase
{
    /**
     * Test validateAddress().
     *
     * @param string       $address
     * @param bool         $expect
     *
     * @dataProvider addressProvider
     */
    public function testValidateAddress($address, $expect)
    {
        $service = $this->_getVhostValidator();
        $test    = $service->validateAddress($address);

        $this->assertEquals($expect, $test);
    }

    /**
     * Get vhost validator.
     *
     * @return VhostValidator
     */
    protected function _getVhostValidator()
    {
        /** @var VhostValidator $validator */
        $validator = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.validator.vhost');
        return $validator;
    }

    /**
     * Test validateIpAddress().
     *
     * @param string $ip
     * @param bool   $expect
     *
     * @dataProvider ipAddressProvider
     */
    public function testValidateIpAddress($ip, $expect)
    {
        $service = $this->_getVhostValidator();
        $test    = $service->validateIpAddress($ip);

        $this->assertEquals($expect, $test);
    }

    /**
     * Provide addresses.
     *
     * @return array
     */
    public function addressProvider()
    {
        return array(
            array('test.de', true),
            array('--__asd.de', false),
        );
    }

    /**
     * Provide ip addresses.
     *
     * @return array
     */
    public function ipAddressProvider()
    {
        return array(
            array('127.0.0.1', true),
            array('8.8.8.', false),
            array('8.8.8.8', true),
            array('2a00:1450:4013:c00::6a', true),
            array('2a00:1450:4013:c00xx6a', false),
        );
    }
}
