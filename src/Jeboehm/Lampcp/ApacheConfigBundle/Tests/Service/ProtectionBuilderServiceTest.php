<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\ProtectionBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProtectionBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionBuilderServiceTest extends WebTestCase
{
    /**
     * Provides protections.
     *
     * @return array
     */
    public function dataProvider()
    {
        $domain = new Domain();
        $domain->setPath('/var/www/test.de');

        $protection = new Protection($domain);
        $user1      = new ProtectionUser($domain, $protection);
        $user2      = new ProtectionUser($domain, $protection);

        $user1
            ->setUsername('test1')
            ->setPassword('pw1');

        $user2
            ->setUsername('test2')
            ->setPassword('pw2');

        $collection = new ArrayCollection(array($user1, $user2));
        $protection
            ->setProtectionuser($collection)
            ->setId(1)
            ->setPath('/var/www/test.de/htdocs/test');

        return array(
            array($protection),
        );
    }

    /**
     * Dataprovider for getIdFromFilename test.
     *
     * @return array
     */
    public function filenameProvider()
    {
        return array(
            array('authuser_93.htpasswd', 93),
            array('authuser_k.htpasswd', null),
            array('kskaod', null),
            array('authuser_11.htpasswd', 11),
        );
    }

    /**
     * Test transformEntity().
     *
     * @param Protection $protection
     *
     * @dataProvider dataProvider
     */
    public function testTransformEntity(Protection $protection)
    {
        $service     = $this->getService();
        $transformed = $service->transformEntity($protection);
        $this->assertInternalType('array', $transformed);
    }

    /**
     * Get service.
     *
     * @return ProtectionBuilderService
     */
    public function getService()
    {
        return $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_protectionbuilder');
    }

    /**
     * Test renderAuthUserFile().
     *
     * @param Protection $protection
     *
     * @dataProvider dataProvider
     */
    public function testRenderAuthUserFile(Protection $protection)
    {
        $service = $this->getService();
        $models  = $service->transformEntity($protection);
        $content = $service->renderAuthUserFile($models);

        foreach ($models as $model) {
            $this->assertContains($model->getUsername() . ':', $content);
            $this->assertContains(':' . $model->getCryptedPassword(), $content);
        }
    }

    /**
     * Test getIdFromFilename().
     *
     * @param string $name
     * @param mixed  $expect
     *
     * @dataProvider filenameProvider
     */
    public function testGetIdFromFilename($name, $expect)
    {
        $this->assertEquals(
            $expect,
            $this
                ->getService()
                ->getIdFromFilename($name)
        );
    }

    public function testRemoveObsoleteAuthUserFiles()
    {
        $files      = array(
            99   => sys_get_temp_dir() . '/conf/authuser_99.passwd',
            39   => sys_get_temp_dir() . '/conf/authuser_39.passwd',
            12   => sys_get_temp_dir() . '/conf/authuser_12.passwd',
            'kk' => sys_get_temp_dir() . '/conf/authuser_kk.passwd',
        );
        $fs         = new Filesystem();
        $domain     = new Domain();
        $protection = new Protection($domain);

        $protection->setId(99);

        $domain
            ->setPath(sys_get_temp_dir())
            ->getProtection()
            ->add($protection);

        $fs->mkdir(sys_get_temp_dir() . '/conf');
        $fs->touch($files);

        $this
            ->getService()
            ->removeObsoleteAuthUserFiles($domain);

        foreach ($files as $key => $file) {
            if (is_int($key) && $key != 99) {
                $this->assertFileNotExists($file);
            } else {
                $this->assertFileExists($file);
                $fs->remove($file);
            }
        }
    }

    /**
     * Test createAuthUserFile().
     */
    public function testCreateAuthUserFile()
    {
        $fs = new Filesystem();
        $fs->mkdir(sys_get_temp_dir() . '/conf');

        $domain         = new Domain();
        $protection     = new Protection($domain);
        $protectionuser = new ProtectionUser($domain, $protection);

        $domain->setPath(sys_get_temp_dir());

        $protection
            ->setId(1)
            ->setProtectionuser(new ArrayCollection(array($protectionuser)));

        $protectionuser
            ->setUsername('test')
            ->setPassword('tester')
            ->setProtection($protection);

        $this
            ->getService()
            ->createAuthUserFile($protection);

        $this->assertFileExists(sys_get_temp_dir() . '/conf/authuser_1.passwd');
    }
}
