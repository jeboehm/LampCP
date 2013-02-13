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
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jeboehm\Lampcp\CoreBundle\Form\Type\PathOptionType;

/**
 * PathOption controller.
 *
 * @Route("/config/pathoption")
 */
class PathOptionController extends AbstractController implements ICrudController {
	/**
	 * Lists all PathOption entities.
	 *
	 * @Route("/", name="config_pathoption")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities PathOption[] */
		$entities = $em
			->getRepository('JeboehmLampcpCoreBundle:PathOption')
			->findByDomain($this->_getSelectedDomain(), array('path' => 'asc'));

		return array(
			'entities' => $entities,
		);
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
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}


		return array(
			'entity' => $entity,
		);
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

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Creates a new PathOption entity.
	 *
	 * @Route("/create", name="config_pathoption_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:PathOption:new.html.twig")
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

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
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
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$editForm = $this->createForm(new PathOptionType(), $entity);

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Edits an existing PathOption entity.
	 *
	 * @Route("/{id}/update", name="config_pathoption_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:PathOption:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity PathOption */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$editForm = $this->createForm(new PathOptionType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_pathoption_edit', array('id' => $id)));
		}

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Deletes a PathOption entity.
	 *
	 * @Route("/{id}/delete", name="config_pathoption_delete")
	 */
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity PathOption */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:PathOption')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find PathOption entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_pathoption'));
	}
}
