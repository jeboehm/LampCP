<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Service\SystemConfigService;

/**
 * SystemConfig controller.
 *
 * @Route("/systemconfig")
 */
class SystemConfigController extends BaseController {
	/**
	 * Get SystemConfigService
	 *
	 * @return SystemConfigService
	 */
	protected function _getSystemConfigService() {
		return $this->get('jboehm_lampcp_core.systemconfigservice');
	}

	/**
	 * Lists all Config entities.
	 *
	 * @Route("/", name="systemconfig")
	 * @Template()
	 */
	public function indexAction() {
		return array('selecteddomain' => $this->_getSelectedDomain(),
					 'config'         => $this->_getSystemConfigService()->getConfigTemplate(),
		);
	}

	/**
	 * Shows all Config entities in edit form.
	 *
	 * @Route("/edit", name="systemconfig_edit")
	 * @Template()
	 */
	public function editAction() {

	}


}
