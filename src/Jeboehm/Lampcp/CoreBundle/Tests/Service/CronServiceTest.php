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

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CronServiceTest
 *
 * @package Jeboehm\Lampcp\CoreBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CronServiceTest extends WebTestCase
{
    /** Name of the entity to check for. */
    const ENTITY_NAME = 'Jeboehm\Lampcp\CoreBundle\Entity\Domain';
    /** @var CronService */
    private $_cs;
    /** @var string */
    private $_name;
    /** @var Domain */
    private $_domain;

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->_cs     = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_core.cronservice');
        $this->_name   = 'test-' . date('YmdHis');
        $this->_domain = $this->_getDomain();
    }

    /**
     * Get new domain.
     *
     * @return Domain
     */
    protected function _getDomain()
    {
        $domain = new Domain();
        $domain
            ->setDomain($this->_name . '.de')
            ->setPath(sys_get_temp_dir());

        return $domain;
    }

    /**
     * Check, that checkEntitesChanged returns true, when cron
     * was never executed.
     *
     * @group database
     */
    public function testNothingModified()
    {
        $check = $this->_cs->checkEntitiesChanged(rand(0, 9999), array(self::ENTITY_NAME));
        $this->assertTrue($check);
    }

    /**
     * Check, that nothing is modified.
     *
     * @group database
     */
    public function testNothingModifiedLastRunSet()
    {
        $this->_cs->updateLastRun($this->_name);
        sleep(2);

        $check = $this->_cs->checkEntitiesChanged($this->_name, array(self::ENTITY_NAME));
        $this->assertFalse($check);
    }

    /**
     * Check modification detection.
     *
     * @group database
     */
    public function testSomethingModified()
    {
        $this->_cs->updateLastRun($this->_name);

        $this->_saveDomain($this->_domain);

        $check = $this->_cs->checkEntitiesChanged($this->_name, array(self::ENTITY_NAME));
        $this->assertTrue($check);
    }

    /**
     * Save domain.
     *
     * @param Domain $domain
     */
    protected function _saveDomain(Domain $domain)
    {
        /** @var EntityManager $em */
        $em = $this
            ->createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $em->persist($domain);
        $em->flush();
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        try {
            $this->_deleteDomain($this->_domain);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        parent::tearDown();
    }

    /**
     * Delete domain.
     *
     * @param Domain $domain
     */
    protected function _deleteDomain(Domain $domain)
    {
        /** @var EntityManager $em */

        if ($domain->getId() > 0) {
            $em = $this
                ->createClient()
                ->getContainer()
                ->get('doctrine.orm.entity_manager');

            $aDomain = $em
                ->getRepository(self::ENTITY_NAME)
                ->findOneBy(array('id' => $domain->getId()));

            $em->remove($aDomain);
            $em->flush();
        }
    }
}
