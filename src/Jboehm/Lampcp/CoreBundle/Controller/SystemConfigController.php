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
		$this->_getConfigForm();

		return array('selecteddomain' => $this->_getSelectedDomain(),
					 'edit_form'      => $this->_getConfigForm()->createView()
		);
	}

	/**
	 * Update configuration
	 *
	 * @Route("/update", name="systemconfig_update")
	 */
	public function updateAction(Request $request) {
		$form = $this->_getConfigForm();
		$form->bind($request);

		if($form->isValid()) {
			$data = $form->getData();

			foreach($data as $name => $value) {
				$name = str_replace('_', '.', $name);
				$this->_getSystemConfigService()->setParameter($name, $value);
			}
		}

		return $this->redirect($this->generateUrl('systemconfig'));
	}

	/**
	 * Get form
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	protected function _getConfigForm() {
		$templ        = $this->_getSystemConfigService()->getConfigTemplate();
		$optionValues = array();

		for($i = 0; $i < count($templ); $i++) {
			foreach($templ[$i]['options'] as $option) {
				$optionValues[str_replace('.', '_', $option['optionname'])] = $option['optionvalue'];
			}
		}

		$builder = $this->createFormBuilder($optionValues);

		foreach($templ as $group) {
			// TODO Gruppe darstellen

			foreach($group['options'] as $option) {
				$builder->add(str_replace('.', '_', $option['optionname']), $option['type'],
							  array('label'    => $option['optionname'],
									'required' => false,
							  ));
			}
		}

		return $builder->getForm();
	}
}
