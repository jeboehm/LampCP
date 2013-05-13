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

use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AbstractController
 *
 * Provides some useful methods for controllers.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractController extends Controller {
    /** @var Logger */
    private $_logger;

    /**
     * Get system config service
     *
     * @return ConfigService
     */
    protected function _getConfigService() {
        return $this->get('config');
    }

    /**
     * Get session
     *
     * @return Session
     */
    protected function _getSession() {
        return $this->get('session');
    }

    /**
     * Add message to flashbag
     *
     * @param string $message
     */
    protected function _addFlash($message) {
        $this
            ->_getSession()
            ->getFlashBag()
            ->add('alert', $message);
    }

    /**
     * Get logger
     *
     * @return Logger
     */
    protected function _getLogger() {
        if (!$this->_logger) {
            $this->_logger = $this->get('logger');
        }

        return $this->_logger;
    }

    /**
     * Get selected domain
     *
     * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain|null
     */
    protected function _getSelectedDomain() {
        /** @var $domainselector DomainselectorService */
        $domainselector = $this->get('jeboehm_lampcp_core.domainselector');

        return $domainselector->getSelected();
    }

    /**
     * Get CryptService
     *
     * @return CryptService
     */
    protected function _getCryptService() {
        return $this->get('jeboehm_lampcp_core.cryptservice');
    }
}
