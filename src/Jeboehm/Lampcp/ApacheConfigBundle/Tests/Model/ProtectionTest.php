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

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection;

/**
 * Class ProtectionTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test id getter / setter.
     */
    public function testId()
    {
        $protection = new Protection();
        $id         = 5;

        $protection->setId($id);
        $this->assertEquals($id, $protection->getId());
    }

    /**
     * Test username getter / setter.
     */
    public function testUsername()
    {
        $protection = new Protection();
        $username   = 'john';

        $protection->setUsername($username);
        $this->assertEquals($username, $protection->getUsername());
    }

    /**
     * Test password getter / setter.
     */
    public function testPassword()
    {
        $protection  = new Protection();
        $password    = 'test123';
        $passwordEnc = crypt($password, base64_encode($password));

        $protection->setPassword($password);
        $this->assertEquals($passwordEnc, $protection->getPassword());
        $this->assertNotEquals($passwordEnc, $password);
    }

    /**
     * Test path getter / setter.
     */
    public function testPath()
    {
        $protection = new Protection();
        $protection->setPath('/var/www');

        $this->assertEquals('/var/www', $protection->getPath());
    }
}
