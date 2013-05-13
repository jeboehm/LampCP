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

use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CertificateBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CertificateBuilderServiceTest extends WebTestCase
{
    /** @var CertificateBuilderService */
    protected $_builder;

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->_builder = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_certificatebuilder');

        $dir = sys_get_temp_dir() . '/certificate-test-' . rand(0, 9999);

        $this->_builder
            ->setStorageDir($dir)
            ->setCryptService($this->getCryptServiceMock());
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->_builder->getStorageDir());
    }

    /**
     * Test the storage directory.
     */
    public function testStorageDirExists()
    {
        $this->assertFileExists($this->_builder->getStorageDir());
    }

    /**
     * Test setCertificates().
     */
    public function testSetCertificates()
    {
        $certificate1 = new Certificate();
        $certificate2 = new Certificate();
        $certificate3 = new Certificate();

        $this->_builder->setCertificates(array($certificate1, $certificate2, $certificate3));

        $this->assertCount(3, $this->_builder->getCertificates());
    }

    /**
     * Test getCertificateById().
     */
    public function testGetCertificateById()
    {
        $certificate1 = new Certificate();
        $certificate2 = new Certificate();
        $certificate3 = new Certificate();

        $certificate1->setId(1);
        $certificate2->setId(2);
        $certificate3->setId(3);

        $this->_builder->setCertificates(array($certificate1, $certificate2, $certificate3));

        $this->assertEquals($this->_builder->getCertificateById(1), $certificate1);

        $this->assertNull($this->_builder->getCertificateById(500));
    }

    /**
     * Test getStorageDir() with empty directory.
     *
     * @expectedException \Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyCertificatePathException
     */
    public function testGetStorageDirEmpty()
    {
        $cb = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_certificatebuilder');

        $cb->getStorageDir();
    }

    /**
     * Provide test-certificates.
     *
     * @return array
     */
    public function certificateProvider()
    {
        $domain = new Domain();

        $certificate1 = new Certificate();
        $certificate1
            ->setId(1)
            ->setName('test1')
            ->setCertificateFile('certTest1')
            ->setCertificateKeyFile('keyTest1');

        $certificate2 = new Certificate();
        $certificate2
            ->setId(2)
            ->setName('test2')
            ->setCertificateFile('certTest2')
            ->setCertificateKeyFile('keyTest2')
            ->setCACertificateFile('caTest2')
            ->setCertificateChainFile('chainTest2');

        $certificate1
            ->getDomain()
            ->add($domain);

        return array(
            array($certificate1),
            array($certificate2),
        );
    }

    /**
     * Test saveCertificate().
     *
     * @param Certificate $certificate
     *
     * @dataProvider certificateProvider
     */
    public function testSaveCertificate(Certificate $certificate)
    {
        $this->_builder->saveCertificate($certificate);

        /*
         * Test certificate file generation.
         */
        if ($certificate->getCertificateChainFile() != '') {
            $this->assertNotEmpty($certificate->getCertificateChainFilePath());
            $this->assertFileExists($certificate->getCertificateChainFilePath());
        }

        if ($certificate->getCACertificateFile() != '') {
            $this->assertNotEmpty($certificate->getCACertificateFilePath());
            $this->assertFileExists($certificate->getCACertificateFilePath());
        }

        if ($certificate->getCertificateKeyFile() != '') {
            $this->assertNotEmpty($certificate->getCertificateKeyFilePath());
            $this->assertFileExists($certificate->getCertificateKeyFilePath());
        }

        if ($certificate->getCertificateFile() != '') {
            $this->assertNotEmpty($certificate->getCertificateFilePath());
            $this->assertFileExists($certificate->getCertificateFilePath());
        }

        $this->assertEquals(
            $this->_builder->getStorageDir() . '/' . $certificate->getId() . '.crt',
            $certificate->getCertificateFilePath()
        );
        $this->assertEquals(
            $this->_builder->getStorageDir() . '/' . $certificate->getId() . '.key',
            $certificate->getCertificateKeyFilePath()
        );

        if ($certificate->getCACertificateFilePath() != '') {
            $this->assertEquals(
                $this->_builder->getStorageDir() . '/' . $certificate->getId() . '.cacrt',
                $certificate->getCACertificateFilePath()
            );
        }

        if ($certificate->getCertificateChainFilePath() != '') {
            $this->assertEquals(
                $this->_builder->getStorageDir() . '/' . $certificate->getId() . '.chain',
                $certificate->getCertificateChainFilePath()
            );
        }

        /*
         * Test, that newly empty files are removed.
         */
        if ($certificate->getCertificateChainFile() != '') {
            $path = $certificate->getCertificateChainFilePath();
            $certificate->setCertificateChainFile('');
            $this->_builder->saveCertificate($certificate);

            $this->assertFileNotExists($path);
        }
    }

    /**
     * Test deleteCertificate().
     *
     * @param Certificate $certificate
     *
     * @dataProvider certificateProvider
     */
    public function testDeleteCertificate(Certificate $certificate)
    {
        $this->_builder->saveCertificate($certificate);

        $paths = array(
            $certificate->getCertificateFilePath(),
            $certificate->getCertificateKeyFilePath(),
            $certificate->getCACertificateFilePath(),
            $certificate->getCertificateChainFilePath(),
        );

        $this->_builder->deleteCertificate($certificate);

        foreach ($paths as $path) {
            if (!empty($path)) {
                $this->assertFileNotExists($path);
            }
        }
    }

    /**
     * Get crypt service mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getCryptServiceMock()
    {
        $cs = $this
            ->getMockBuilder('Jeboehm\Lampcp\CoreBundle\Service\CryptService')
            ->disableOriginalConstructor()
            ->setMethods(array('encrypt', 'decrypt'))
            ->getMock();

        $cs
            ->expects($this->any())
            ->method('decrypt')
            ->will($this->returnValue('decryptedData'));

        $cs
            ->expects($this->any())
            ->method('encrypt')
            ->will($this->returnValue('encryptedData'));

        return $cs;
    }

    /**
     * Test removeUnusedCertificates().
     */
    public function testRemoveUnusedCertificates()
    {
        $files = array(
            $this->_builder->getStorageDir() . '/5.crt',
            $this->_builder->getStorageDir() . '/5.key',
            $this->_builder->getStorageDir() . '/5.cacrt',
            $this->_builder->getStorageDir() . '/5.chain',
            $this->_builder->getStorageDir() . '/1.crt',
            $this->_builder->getStorageDir() . '/askdjasd.crt',
        );

        $certificate1 = new Certificate();
        $certificate2 = new Certificate();
        $certificate3 = new Certificate();

        $certificate1->setId(1);
        $certificate2->setId(2);
        $certificate3->setId(3);

        $this->_builder->setCertificates(array($certificate1, $certificate2, $certificate3));

        $fs = new Filesystem();
        $fs->touch($files);

        $this->_builder->removeUnusedCertificateFiles();

        $this->assertFileNotExists($this->_builder->getStorageDir() . '/5.chain');
        $this->assertFileNotExists($this->_builder->getStorageDir() . '/5.crt');
        $this->assertFileNotExists($this->_builder->getStorageDir() . '/5.cacrt');
        $this->assertFileNotExists($this->_builder->getStorageDir() . '/5.key');
        $this->assertFileExists($this->_builder->getStorageDir() . '/1.crt');
        $this->assertFileExists($this->_builder->getStorageDir() . '/askdjasd.crt');
    }

    /**
     * Test buildCertificates().
     *
     * @param Certificate $certificate
     *
     * @dataProvider certificateProvider
     */
    public function testBuildCertificates(Certificate $certificate)
    {
        $this->_builder
            ->setCertificates(array($certificate))
            ->buildCertificates();

        if ($certificate
            ->getDomain()
            ->count() < 1
        ) {
            $this->assertEmpty($certificate->getCertificateFilePath());
        } else {
            $this->assertNotEmpty($certificate->getCertificateFilePath());
        }
    }
}
