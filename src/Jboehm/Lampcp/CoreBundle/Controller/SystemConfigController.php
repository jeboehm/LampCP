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
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

/**
 * SystemConfig controller.
 *
 * @Route("/config/system")
 */
class SystemConfigController extends AbstractController {
	/**
	 * Lists all Config entities.
	 *
	 * @Route("/", name="systemconfig")
	 * @Template()
	 */
	public function indexAction() {
		$groups = $this->getDoctrine()->getRepository('JboehmLampcpConfigBundle:ConfigGroup')->findAll();

		return $this->_getReturn(array(
									  'groups' => $groups,
								 ));
	}

	/**
	 * Shows all Config entities in edit form.
	 *
	 * @Route("/edit", name="systemconfig_edit")
	 * @Template()
	 */
	public function editAction() {
		return $this->_getReturn(array(
									  'form' => $this
										  ->_getConfigService()
										  ->getForm()
										  ->createView(),
								 ));
	}

	/**
	 * Update configuration
	 *
	 * @Route("/update", name="systemconfig_update")
	 */
	public function updateAction(Request $request) {
		$form = $this->_getConfigService()->getForm();
		$form->bind($request);

		if($form->isValid()) {
			$formdata = $form->getData();

			foreach($formdata['configentity'] as $entity) {
				/** @var $entity ConfigEntity */
				$name = $entity->getConfiggroup()->getName() . '.' . $entity->getName();
				$this->_getConfigService()->setParameter($name, $entity->getValue());
			}
		}

		return $this->redirect($this->generateUrl('systemconfig'));
	}
}
