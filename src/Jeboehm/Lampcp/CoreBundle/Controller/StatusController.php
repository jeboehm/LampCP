<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\Cron;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StatusController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class StatusController extends AbstractController
{
    /**
     * Shows status page.
     *
     * @Route("/", name="status")
     * @Route("/", name="default")
     * @Template()
     */
    public function indexAction()
    {
        $cronjobs = $this
            ->_getCronRepository()
            ->findAll();

        return array(
            'cronjobs' => $cronjobs,
        );
    }

    /**
     * Get cron repository.
     *
     * @return EntityRepository
     */
    protected function _getCronRepository()
    {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Cron');
    }

    /**
     * Saves the domain to domainselector.
     *
     * @Route("/config/setdomain/{domain}", name="status_set_domain")
     */
    public function setDomainAction(Domain $domain)
    {
        /** @var $domainselector DomainselectorService */
        $domainselector = $this->get('jeboehm_lampcp_core.domainselector');
        $domainselector->setDomain($domain);

        return $this->redirect($this->generateUrl('status'));
    }

    /**
     * Set force to true in Cron entity.
     *
     * @Route("/config/forcecron/{cron}", name="status_force_cron")
     */
    public function setCronForceAction(Cron $cron)
    {
        return $this->redirect($this->generateUrl('status'));
    }
}
