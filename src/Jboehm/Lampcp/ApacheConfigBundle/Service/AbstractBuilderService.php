<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Jboehm\Lampcp\CoreBundle\Service\SystemConfigService;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;

abstract class AbstractBuilderService {
	/** @var TwigEngine */
	private $_templating;

	/** @var EntityManager */
	private $_doctrine;

	/** @var SystemConfigService */
	private $_systemconfigservice;

	/** @var Logger */
	private $_logger;

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
	 * @return \Jboehm\Lampcp\CoreBundle\Service\SystemConfigService
	 */
	protected function _getSystemConfigService() {
		return $this->_systemconfigservice;
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
	 * @param $templating
	 * @param $doctrine
	 * @param $systemconfigservice
	 * @param $logger
	 */
	public function __construct($templating, $doctrine, $systemconfigservice, $logger) {
		$this->_templating          = $templating;
		$this->_doctrine            = $doctrine;
		$this->_systemconfigservice = $systemconfigservice;
		$this->_logger              = $logger;
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
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Domain[]
	 */
	protected function _getAllDomains() {
		/** @var $domains Domain[] */
		$domains = $this->_getDoctrine()->getRepository('JboehmLampcpCoreBundle:Domain')->findAll();

		return $domains;
	}

	/**
	 * Get all Subdomains
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Subdomain[]
	 */
	protected function _getAllSubdomains() {
		/** @var $subdomains Subdomain[] */
		$subdomains = $this->_getDoctrine()->getRepository('JboehmLampcpCoreBundle:Subdomain')->findAll();

		return $subdomains;
	}
}
