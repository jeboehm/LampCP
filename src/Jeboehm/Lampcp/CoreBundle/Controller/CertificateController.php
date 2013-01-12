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
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Form\CertificateType;

/**
 * Certificate controller.
 *
 * @Route("/config/certificate")
 */
class CertificateController extends AbstractController {
	/**
	 * Lists all Certificate entities.
	 *
	 * @Route("/", name="config_certificate")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities Certificate[] */
		$entities = $em->getRepository('JeboehmLampcpCoreBundle:Certificate')->findAll();

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a Certificate entity.
	 *
	 * @Route("/{id}/show", name="config_certificate_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Certificate */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Certificate')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		$privKey = $entity->getCertificateKeyFile();

		if(!empty($privKey)) {
			$entity->setCertificateKeyFile($this->_getCryptService()->decrypt($privKey));
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Displays a form to create a new Certificate entity.
	 *
	 * @Route("/new", name="config_certificate_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Certificate();
		$form   = $this->createForm(new CertificateType(), $entity);

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new Certificate entity.
	 *
	 * @Route("/create", name="config_certificate_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Certificate:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Certificate();
		$form   = $this->createForm(new CertificateType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_certificate_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing Certificate entity.
	 *
	 * @Route("/{id}/edit", name="config_certificate_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Certificate */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Certificate')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		$privKey = $entity->getCertificateKeyFile();

		if(!empty($privKey)) {
			$entity->setCertificateKeyFile($this->_getCryptService()->decrypt($privKey));
		}

		$editForm   = $this->createForm(new CertificateType(), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Edits an existing Certificate entity.
	 *
	 * @Route("/{id}/update", name="config_certificate_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Certificate:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Certificate */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Certificate')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new CertificateType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$privKey = $this->_getCryptService()->encrypt($entity->getCertificateKeyFile());
			$entity->setCertificateKeyFile($privKey);

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_certificate_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Deletes a Certificate entity.
	 *
	 * @Route("/{id}/delete", name="config_certificate_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity Certificate */
			$entity = $em->getRepository('JeboehmLampcpCoreBundle:Certificate')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find Certificate entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_certificate'));
	}

	/**
	 * Get delete form
	 *
	 * @param int $id
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
