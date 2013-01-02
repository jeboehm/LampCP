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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\Log;

/**
 * Log controller.
 *
 * @Route("/config/log")
 */
class LogController extends BaseController {
	/**
	 * Lists all Log entities.
	 *
	 * @Route("/", name="log")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities Log[] */
		$entities = $em
			->getRepository('JboehmLampcpCoreBundle:Log')->findBy(array(), array('time' => 'desc',
																				 'id'   => 'desc'), 100);

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a Log entity.
	 *
	 * @Route("/{id}/show", name="log_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Log */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Log')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Log entity.');
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
								 ));
	}
}
