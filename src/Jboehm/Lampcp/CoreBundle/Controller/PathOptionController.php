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
use Jboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jboehm\Lampcp\CoreBundle\Form\PathOptionType;

/**
 * PathOption controller.
 *
 * @Route("/config/pathoption")
 */
class PathOptionController extends BaseController {
	/**
	 * Lists all PathOption entities.
	 *
	 * @Route("/", name="config_pathoption")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JboehmLampcpCoreBundle:PathOption')->findByDomain($this->_getSelectedDomain());

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a PathOption entity.
	 *
	 * @Route("/{id}/show", name="config_pathoption_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity PathOption */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Displays a form to create a new PathOption entity.
	 *
	 * @Route("/new", name="config_pathoption_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new PathOption($this->_getSelectedDomain());
		$form   = $this->createForm(new PathOptionType(), $entity);

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new PathOption entity.
	 *
	 * @Route("/create", name="config_pathoption_create")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:PathOption:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new PathOption($this->_getSelectedDomain());
		$form   = $this->createForm(new PathOptionType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_pathoption_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing PathOption entity.
	 *
	 * @Route("/{id}/edit", name="config_pathoption_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity PathOption */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$editForm   = $this->createForm(new PathOptionType(), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Edits an existing PathOption entity.
	 *
	 * @Route("/{id}/update", name="config_pathoption_update")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:PathOption:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity PathOption */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new PathOptionType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_pathoption_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Deletes a PathOption entity.
	 *
	 * @Route("/{id}/delete", name="config_pathoption_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity PathOption */
			$entity = $em->getRepository('JboehmLampcpCoreBundle:PathOption')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find PathOption entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_pathoption'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
