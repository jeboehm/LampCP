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
use Jboehm\Lampcp\CoreBundle\Service\SystemConfigService;

abstract class AbstractBuilderService {
	/** @var TwigEngine */
	private $_templating;

	/** @var EntityManager */
	private $_doctrine;

	/** @var SystemConfigService */
	private $_systemconfigservice;

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
	 * @param $templating
	 * @param $doctrine
	 * @param $systemconfigservice
	 */
	public function __construct($templating, $doctrine, $systemconfigservice) {
		$this->_templating          = $templating;
		$this->_doctrine            = $doctrine;
		$this->_systemconfigservice = $systemconfigservice;
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
}
