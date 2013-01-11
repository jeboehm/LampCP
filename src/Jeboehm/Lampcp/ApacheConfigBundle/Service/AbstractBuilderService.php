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

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderInterface;

abstract class AbstractBuilderService implements BuilderInterface {
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
	 * @return \Symfony\Bundle\TwigBundle\TwigEngine
	 */
	protected function _getTemplating() {
		return $this->_templating;
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function _getDoctrine() {
		return $this->_doctrine;
	}

	/**
	 * @return \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService
	 */
	protected function _getConfigService() {
		return $this->_configService;
	}

	/**
	 * Get Logger
	 *
	 * @return Logger
	 */
	protected function _getLogger() {
		return $this->_logger;
	}

	/**
	 * Get cryptservice
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Service\CryptService
	 */
	protected function _getCryptService() {
		return $this->_cryptService;
	}

	/**
	 * Konstruktor
	 *
	 * @param \Symfony\Bundle\TwigBundle\TwigEngine              $templating
	 * @param \Doctrine\ORM\EntityManager                        $doctrine
	 * @param \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService $configService
	 * @param \Symfony\Bridge\Monolog\Logger                     $logger
	 * @param \Jeboehm\Lampcp\CoreBundle\Service\CryptService    $cs
	 */
	public function __construct(TwigEngine $templating, EntityManager $doctrine,
								ConfigService $configService, Logger $logger,
								CryptService $cs) {
		$this->_templating    = $templating;
		$this->_doctrine      = $doctrine;
		$this->_configService = $configService;
		$this->_logger        = $logger;
		$this->_cryptService  = $cs;
	}

	/**
	 * Render Template
	 *
	 * @param string $template
	 * @param array  $options
	 *
	 * @return string
	 */
	protected function _renderTemplate($template, array $options) {
		return $this->_getTemplating()->render($template, $options);
	}

	/**
	 * Get all domains
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain[]
	 */
	protected function _getAllDomains() {
		/** @var $domains Domain[] */
		$domains = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Domain')->findAll();

		return $domains;
	}

	/**
	 * Get all Subdomains
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain[]
	 */
	protected function _getAllSubdomains() {
		/** @var $subdomains Subdomain[] */
		$subdomains = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Subdomain')->findAll();

		return $subdomains;
	}
}
