<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Tests\Service;

use Jeboehm\Lampcp\CoreBundle\Exception\WrongEncryptionKeyException;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CryptServiceTest
 *
 * @package Jeboehm\Lampcp\CoreBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CryptServiceTest extends WebTestCase {
    /** @var CryptService */
    protected $_cs;

    /**
     * Set up.
     */
    public function setUp() {
        $this->_cs = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_core.cryptservice');
    }

    /**
     * Test data encryption.
     */
    public function testEncrypt() {
        $result = $this->_cs->encrypt('randomdata');
        $this->assertNotEquals('randomdata', $result);

        return $result;
    }

    /**
     * Test data decryption.
     *
     * @depends testEncrypt
     */
    public function testDecrypt($encrypted) {
        $result = $this->_cs->decrypt($encrypted);
        $this->assertEquals('randomdata', $result);
    }

    /**
     * Expect exception on invalid decrypted data.
     *
     * @expectedException \Jeboehm\Lampcp\CoreBundle\Exception\WrongEncryptionKeyException
     */
    public function testDecryptFailure() {
        $this->_cs->decrypt(rand(0, 9999));
    }
}
