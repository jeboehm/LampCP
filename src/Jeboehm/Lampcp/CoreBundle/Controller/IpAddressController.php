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

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Form\IpAddressType;

/**
 * IpAddress controller.
 *
 * @Route("/config/ipaddress")
 */
class IpAddressController extends AbstractController implements ICrudController {
	/**
	 * Lists all IpAddress entities.
	 *
	 * @Route("/", name="config_ipaddress")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JeboehmLampcpCoreBundle:IpAddress')->findAll();

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a IpAddress entity.
	 *
	 * @Route("/{id}/show", name="config_ipaddress_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity IpAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:IpAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find IpAddress entity.');
		}


		return $this->_getReturn(array(
									  'entity' => $entity,
								 ));
	}

	/**
	 * Displays a form to create a new IpAddress entity.
	 *
	 * @Route("/new", name="config_ipaddress_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new IpAddress($this->_getSelectedDomain());
		$form   = $this->createForm(new IpAddressType(), $entity);

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new IpAddress entity.
	 *
	 * @Route("/create", name="config_ipaddress_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:IpAddress:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new IpAddress();
		$form   = $this->createForm(new IpAddressType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_ipaddress_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing IpAddress entity.
	 *
	 * @Route("/{id}/edit", name="config_ipaddress_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity IpAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:IpAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find IpAddress entity.');
		}

		$editForm = $this->createForm(new IpAddressType(), $entity);

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Edits an existing IpAddress entity.
	 *
	 * @Route("/{id}/update", name="config_ipaddress_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:IpAddress:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity IpAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:IpAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find IpAddress entity.');
		}

		$editForm = $this->createForm(new IpAddressType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_ipaddress_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Deletes a IpAddress entity.
	 *
	 * @Route("/{id}/delete", name="config_ipaddress_delete")
	 */
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity IpAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:IpAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find IpAddress entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_ipaddress'));
	}
}
