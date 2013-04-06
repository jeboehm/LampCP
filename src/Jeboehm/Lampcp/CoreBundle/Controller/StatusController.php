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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;

/**
 * Class StatusController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class StatusController extends AbstractController {
    /**
     * Shows status page.
     *
     * @Route("/", name="status")
     * @Route("/", name="default")
     * @Template()
     * @return array
     */
    public function indexAction() {
        $cronjobs = $this
            ->_getCronRepository()
            ->findAll();

        return array(
            'cronjobs' => $cronjobs,
        );
    }

    /**
     * Saves the domain to domainselector
     *
     * @Route("/config/setdomain/{domain}", name="set_domain")
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setDomainAction(Domain $domain) {
        /** @var $domainselector DomainselectorService */
        $domainselector = $this->get('jeboehm_lampcp_core.domainselector');
        $domainselector->setDomain($domain);

        return $this->redirect($this->generateUrl('status'));
    }

    /**
     * Get cron repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function _getCronRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Cron');
    }
}
