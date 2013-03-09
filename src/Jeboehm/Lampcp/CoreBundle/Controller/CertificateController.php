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
use Jeboehm\Lampcp\CoreBundle\Form\Type\CertificateType;

/**
 * Certificate controller.
 *
 * @Route("/config/certificate")
 */
class CertificateController extends AbstractController implements ICrudController {
	/**
	 * Lists all Certificate entities.
	 *
	 * @Route("/", name="config_certificate")
	 * @Template()
	 */
	public function indexAction() {
		/** @var $entities Certificate[] */
		$entities = $this->_getRepository()->findBy(array(), array('name' => 'asc'));

		return array(
			'entities' => $entities,
		);
	}

	/**
	 * Finds and displays a Certificate entity.
	 *
	 * @Route("/{id}/show", name="config_certificate_show")
	 * @Template()
	 */
	public function showAction($id) {
		/** @var $entity Certificate */
		$entity = $this->_getRepository()->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		try {
			$privKey = $entity->getCertificateKeyFile();

			if(!empty($privKey)) {
				$entity->setCertificateKeyFile($this->_getCryptService()->decrypt($privKey));
			}
		} catch(\Exception $e) {

		}


		return array(
			'entity' => $entity,
		);
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

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
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
			$privKey = $entity->getCertificateKeyFile();

			if(!empty($privKey)) {
				$entity->setCertificateKeyFile($this->_getCryptService()->encrypt($privKey));
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_certificate_show', array('id' => $entity->getId())));
		}

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Displays a form to edit an existing Certificate entity.
	 *
	 * @Route("/{id}/edit", name="config_certificate_edit")
	 * @Template()
	 */
	public function editAction($id) {
		/** @var $entity Certificate */
		$entity = $this->_getRepository()->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		try {
			$privKey = $entity->getCertificateKeyFile();

			if(!empty($privKey)) {
				$entity->setCertificateKeyFile($this->_getCryptService()->decrypt($privKey));
			}
		} catch(\Exception $e) {

		}

		$editForm = $this->createForm(new CertificateType(), $entity);

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Edits an existing Certificate entity.
	 *
	 * @Route("/{id}/update", name="config_certificate_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Certificate:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		/** @var $entity Certificate */
		$em     = $this->getDoctrine()->getManager();
		$entity = $this->_getRepository()->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		$editForm = $this->createForm(new CertificateType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$privKey = $this->_getCryptService()->encrypt($entity->getCertificateKeyFile());
			$entity->setCertificateKeyFile($privKey);

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_certificate_edit', array('id' => $id)));
		}

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Deletes a Certificate entity.
	 *
	 * @Route("/{id}/delete", name="config_certificate_delete")
	 */
	public function deleteAction($id) {
		/** @var $entity Certificate */
		$em     = $this->getDoctrine()->getManager();
		$entity = $this->_getRepository()->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Certificate entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_certificate'));
	}

	/**
	 * Get repository
	 *
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	protected function _getRepository() {
		return $this->getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Certificate');
	}
}
