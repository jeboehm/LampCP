<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Class AbstractBuilderService
 *
 * Provides useful methods for configuration building services.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractBuilderService
{
    /** @var TwigEngine */
    private $_templating;
    /** @var EntityManager */
    private $_doctrine;
    /** @var ConfigService */
    private $_configService;
    /** @var Logger */
    private $_logger;
    /** @var CryptService */
    private $_cryptService;

    /**
     * Constructor.
     *
     * @param TwigEngine    $templating
     * @param EntityManager $doctrine
     * @param ConfigService $configService
     * @param Logger        $logger
     * @param CryptService  $cs
     */
    public function __construct(
        TwigEngine $templating,
        EntityManager $doctrine,
        ConfigService $configService,
        Logger $logger,
        CryptService $cs
    ) {
        $this->_templating = $templating;
        $this->_doctrine = $doctrine;
        $this->_configService = $configService;
        $this->_logger = $logger;
        $this->_cryptService = $cs;
    }

    /**
     * Get config service.
     *
     * @return ConfigService
     */
    protected function _getConfigService()
    {
        return $this->_configService;
    }

    /**
     * Get Logger.
     *
     * @return Logger
     */
    protected function _getLogger()
    {
        return $this->_logger;
    }

    /**
     * Get cryptservice.
     *
     * @return CryptService
     */
    protected function _getCryptService()
    {
        return $this->_cryptService;
    }

    /**
     * Render Template.
     *
     * @param string $template
     * @param array  $options
     *
     * @return string
     */
    protected function _renderTemplate($template, array $options)
    {
        return $this
            ->_getTemplating()
            ->render($template, $options);
    }

    /**
     * Get twig engine.
     *
     * @return TwigEngine
     */
    protected function _getTemplating()
    {
        return $this->_templating;
    }

    /**
     * Get all domains.
     *
     * @return Domain[]
     */
    protected function _getAllDomains()
    {
        /** @var $domains Domain[] */
        $domains = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain')
            ->findAll();

        return $domains;
    }

    /**
     * Get entity manager.
     *
     * @return EntityManager
     */
    protected function _getDoctrine()
    {
        return $this->_doctrine;
    }

    /**
     * Get all Subdomains.
     *
     * @return Subdomain[]
     */
    protected function _getAllSubdomains()
    {
        /** @var $subdomains Subdomain[] */
        $subdomains = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Subdomain')
            ->findAll();

        return $subdomains;
    }
}
