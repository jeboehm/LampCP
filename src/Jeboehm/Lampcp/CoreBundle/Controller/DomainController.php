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
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Form\DomainType;

/**
 * Domain controller.
 *
 * @Route("/config/domain")
 */
class DomainController extends AbstractController implements ICrudController {
	/**
	 * Lists all Domain entities.
	 *
	 * @Route("/", name="config_domain")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JeboehmLampcpCoreBundle:Domain')->findAll();

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a Domain entity.
	 *
	 * @Route("/{id}/show", name="config_domain_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Domain */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Domain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Domain entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Displays a form to create a new Domain entity.
	 *
	 * @Route("/new", name="config_domain_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Domain();
		$entity->setPath('(auto)');
		$form = $this->createForm(new DomainType(), $entity);


		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new Domain entity.
	 *
	 * @Route("/create", name="config_domain_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Domain:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Domain();
		$form   = $this->createForm(new DomainType(), $entity);
		$form->bind($request);
		$entity->setPath($this->_getSystemWebPath() . '/' . $entity->getDomain());

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_domain_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing Domain entity.
	 *
	 * @Route("/{id}/edit", name="config_domain_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Domain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Domain entity.');
		}

		$editForm   = $this->createForm(new DomainType(true), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Edits an existing Domain entity.
	 *
	 * @Route("/{id}/update", name="config_domain_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Domain:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Domain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Domain entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new DomainType(true), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_domain_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Deletes a Domain entity.
	 *
	 * @Route("/{id}/delete", name="config_domain_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em     = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('JeboehmLampcpCoreBundle:Domain')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find Domain entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_domain'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}

	/**
	 * Get configured system web path
	 *
	 * @return string
	 */
	protected function _getSystemWebPath() {
		return $this->_getConfigService()->getParameter('apache.pathwww');
	}
}
