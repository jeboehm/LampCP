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

use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service\VhostBuilderServiceTest as ParentVhostBuilderTest;

/**
 * Class VhostBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderServiceTest extends ParentVhostBuilderTest
{
    /**
     * Test getSingleCertificateWithDomainsAssigned().
     *
     * @dataProvider serviceProvider
     */
    public function testGetSingleCertificateWithDomainsAssigned(VhostBuilderService $service)
    {
        $service->collectVhostModels();
        $cert = $service->getSingleCertificateWithDomainsAssigned();
        $this->assertInstanceOf('\Jeboehm\Lampcp\CoreBundle\Entity\Certificate', $cert);

        $this->assertNull($this->getVhostBuilderService()->getSingleCertificateWithDomainsAssigned());
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService
     */
    protected function getVhostBuilderService()
    {
        /** @var VhostBuilderService $service */
        $service = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_vhostbuilder');

        return $service;
    }
}
