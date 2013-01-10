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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\Protection;
use Jboehm\Lampcp\CoreBundle\Entity\ProtectionUser;
use Jboehm\Lampcp\CoreBundle\Form\ProtectionUserType;

/**
 * ProtectionUser controller.
 *
 * @Route("/config/protectionuser")
 */
class ProtectionUserController extends AbstractController {
	/**
	 * @param int $protectionId
	 *
	 * @return Protection
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getProtection($protectionId) {
		/** @var $protection Protection */
		$protection = $this
			->getDoctrine()
			->getRepository('JboehmLampcpCoreBundle:Protection')
			->findOneBy(array('id' => intval($protectionId)));

		if(!$protection) {
			throw $this->createNotFoundException();
		}

		return $protection;
	}

	/**
	 * Lists all ProtectionUser entities.
	 *
	 * @Route("/{protectionid}/", name="config_protectionuser")
	 * @Template()
	 */
	public function indexAction($protectionid) {
		/** @var $protection Protection */
		$protection = $this->_getProtection($protectionid);
		$em         = $this->getDoctrine()->getManager();

		$entities = $em
			->getRepository('JboehmLampcpCoreBundle:ProtectionUser')
			->findBy(array('protection' => $protection));

		return $this->_getReturn(array(
									  'entities'   => $entities,
									  'protection' => $protection,
								 ));
	}

	/**
	 * Finds and displays a ProtectionUser entity.
	 *
	 * @Route("/{id}/show", name="config_protectionuser_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity ProtectionUser */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:ProtectionUser')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find ProtectionUser entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Displays a form to create a new ProtectionUser entity.
	 *
	 * @Route("/{protectionid}/new", name="config_protectionuser_new")
	 * @Template()
	 */
	public function newAction($protectionid) {
		/** @var $protection Protection */
		$protection = $this->_getProtection($protectionid);
		$entity     = new ProtectionUser($this->_getSelectedDomain(), $protection);
		$form       = $this->createForm(new ProtectionUserType(), $entity);

		return $this->_getReturn(array(
									  'entity'     => $entity,
									  'form'       => $form->createView(),
									  'protection' => $protection,
								 ));
	}

	/**
	 * Creates a new ProtectionUser entity.
	 *
	 * @Route("/{protectionid}/create", name="config_protectionuser_create")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:ProtectionUser:new.html.twig")
	 */
	public function createAction(Request $request, $protectionid) {
		/** @var $protection Protection */
		$protection = $this->_getProtection($protectionid);
		$entity     = new ProtectionUser($this->_getSelectedDomain(), $protection);
		$form       = $this->createForm(new ProtectionUserType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protectionuser_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity'     => $entity,
									  'form'       => $form->createView(),
									  'protection' => $protection,
								 ));
	}

	/**
	 * Displays a form to edit an existing ProtectionUser entity.
	 *
	 * @Route("/{id}/edit", name="config_protectionuser_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity ProtectionUser */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:ProtectionUser')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find ProtectionUser entity.');
		}

		$editForm   = $this->createForm(new ProtectionUserType(true), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Edits an existing ProtectionUser entity.
	 *
	 * @Route("/{id}/update", name="config_protectionuser_update")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:ProtectionUser:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity ProtectionUser */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:ProtectionUser')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find ProtectionUser entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new ProtectionUserType(true), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protectionuser_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Deletes a ProtectionUser entity.
	 *
	 * @Route("/{id}/delete", name="config_protectionuser_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity ProtectionUser */
			$entity = $em->getRepository('JboehmLampcpCoreBundle:ProtectionUser')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find ProtectionUser entity.');
			}

			$protectionid = $entity->getProtection()->getId();

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_protectionuser', array('protectionid' => $protectionid)));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
