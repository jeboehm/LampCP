<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Tests\Service;

use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\LightyConfigBundle\Service\CertificateBuilderService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CertificateBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Tests\Service
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
            ->get('jeboehm_lampcp_lighty_config_certificatebuilder');

        $dir = sys_get_temp_dir() . '/certificate-test-' . rand(0, 9999);

        $this->_builder
            ->setStorageDir($dir)
            ->setCryptService($this->getCryptServiceMock());
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
     * Test, that key file is merged into cert,
     * and chain is merged into cacertfile.
     *
     * @param Certificate $certificate
     *
     * @dataProvider certificateProvider
     */
    public function testCertificateFilesMerged(Certificate $certificate)
    {
        $this->_builder->saveCertificate($certificate);

        $keyfile = $certificate->getCertificateKeyFile();
        $chainfile = $certificate->getCertificateChainFile();

        if (!empty($keyfile)) {
            $expect = $certificate->getCertificateFile() . PHP_EOL . file_get_contents(
                $certificate->getCertificateKeyFilePath()
            );
            $this->assertEquals($expect, file_get_contents($certificate->getCertificateFilePath()));
        }

        if (!empty($chainfile)) {
            $expect = $certificate->getCACertificateFile() . PHP_EOL . $certificate->getCertificateChainFile();
            $this->assertEquals($expect, file_get_contents($certificate->getCACertificateFilePath()));
        }
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->_builder->getStorageDir());
    }
}
