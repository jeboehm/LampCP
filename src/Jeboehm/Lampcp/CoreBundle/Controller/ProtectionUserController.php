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
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser;
use Jeboehm\Lampcp\CoreBundle\Form\Type\ProtectionUserType;

/**
 * ProtectionUser controller.
 *
 * @Route("/config/protectionuser")
 */
class ProtectionUserController extends AbstractController {
	/**
	 * Lists all ProtectionUser entities.
	 *
	 * @Route("/{protection}/", name="config_protectionuser")
	 * @Template()
	 */
	public function indexAction(Protection $protection) {
		$entities = $this->_getRepository()->findBy(array('protection' => $protection), array('username' => 'asc'));

		return array(
			'entities'   => $entities,
			'protection' => $protection,
		);
	}

	/**
	 * Finds and displays a ProtectionUser entity.
	 *
	 * @Route("/{entity}/show", name="config_protectionuser_show")
	 * @Template()
	 */
	public function showAction(ProtectionUser $entity) {
		return array(
			'entity' => $entity,
		);
	}

	/**
	 * Displays a form to create a new ProtectionUser entity.
	 *
	 * @Route("/{protection}/new", name="config_protectionuser_new")
	 * @Template()
	 */
	public function newAction(Protection $protection) {
		$entity = new ProtectionUser($this->_getSelectedDomain(), $protection);
		$form   = $this->createForm(new ProtectionUserType(), $entity);

		return array(
			'entity'     => $entity,
			'form'       => $form->createView(),
			'protection' => $protection,
		);
	}

	/**
	 * Creates a new ProtectionUser entity.
	 *
	 * @Route("/{protection}/create", name="config_protectionuser_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:ProtectionUser:new.html.twig")
	 */
	public function createAction(Request $request, Protection $protection) {
		$entity = new ProtectionUser($this->_getSelectedDomain(), $protection);
		$form   = $this->createForm(new ProtectionUserType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$entity->setPassword($this->_getCryptService()->encrypt($entity->getPassword()));

			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protectionuser_show', array('entity' => $entity->getId())));
		}

		return array(
			'entity'     => $entity,
			'form'       => $form->createView(),
			'protection' => $protection,
		);
	}

	/**
	 * Displays a form to edit an existing ProtectionUser entity.
	 *
	 * @Route("/{entity}/edit", name="config_protectionuser_edit")
	 * @Template()
	 */
	public function editAction(ProtectionUser $entity) {
		$editForm = $this->createForm(new ProtectionUserType(), $entity);

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Edits an existing ProtectionUser entity.
	 *
	 * @Route("/{entity}/update", name="config_protectionuser_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:ProtectionUser:edit.html.twig")
	 */
	public function updateAction(Request $request, ProtectionUser $entity) {
		$oldPassword = $entity->getPassword();
		$editForm    = $this->createForm(new ProtectionUserType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em = $this->getDoctrine()->getManager();

			if(!$entity->getPassword()) {
				$entity->setPassword($oldPassword);
			} else {
				$entity->setPassword($this->_getCryptService()->encrypt($entity->getPassword()));
			}

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protectionuser_edit', array('entity' => $entity->getId())));
		}

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Deletes a ProtectionUser entity.
	 *
	 * @Route("/{entity}/delete", name="config_protectionuser_delete")
	 */
	public function deleteAction(ProtectionUser $entity) {
		$em = $this->getDoctrine()->getManager();
		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_protectionuser', array('protection' => $entity->getProtection()->getId())));
	}

	/**
	 * Get repository
	 *
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	private function _getRepository() {
		return $this->getDoctrine()->getRepository('JeboehmLampcpCoreBundle:ProtectionUser');
	}
}
