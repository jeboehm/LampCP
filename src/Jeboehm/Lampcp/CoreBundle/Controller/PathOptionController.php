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
class PathOptionController extends AbstractController {
	/**
	 * Lists all PathOption entities.
	 *
	 * @Route("/", name="config_pathoption")
	 * @Template()
	 */
	public function indexAction() {
		/** @var $entities PathOption[] */
		$entities = $this->_getRepository()->findBy(array('domain' => $this->_getSelectedDomain()), array('path' => 'asc'));

		return array(
			'entities' => $entities,
		);
	}

	/**
	 * Finds and displays a PathOption entity.
	 *
	 * @Route("/{entity}/show", name="config_pathoption_show")
	 * @Template()
	 */
	public function showAction(PathOption $entity) {
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

			return $this->redirect($this->generateUrl('config_pathoption_show', array('entity' => $entity->getId())));
		}

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Displays a form to edit an existing PathOption entity.
	 *
	 * @Route("/{entity}/edit", name="config_pathoption_edit")
	 * @Template()
	 */
	public function editAction(PathOption $entity) {
		$editForm = $this->createForm(new PathOptionType(), $entity);

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Edits an existing PathOption entity.
	 *
	 * @Route("/{entity}/update", name="config_pathoption_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:PathOption:edit.html.twig")
	 */
	public function updateAction(Request $request, PathOption $entity) {
		$editForm = $this->createForm(new PathOptionType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_pathoption_edit', array('entity' => $entity->getId())));
		}

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Deletes a PathOption entity.
	 *
	 * @Route("/{entity}/delete", name="config_pathoption_delete")
	 */
	public function deleteAction(PathOption $entity) {
		$em = $this->getDoctrine()->getManager();
		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_pathoption'));
	}

	/**
	 * Get repository
	 *
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	private function _getRepository() {
		return $this->getDoctrine()->getRepository('JeboehmLampcpCoreBundle:PathOption');
	}
}
