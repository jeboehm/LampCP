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
use Jeboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jeboehm\Lampcp\CoreBundle\Form\MailAccountType;
use Jeboehm\Lampcp\CoreBundle\Entity\MailAccountRepository;

/**
 * MailAccount controller.
 *
 * @Route("/config/mailaccount")
 */
class MailAccountController extends AbstractController {
	/**
	 * Lists all MailAccount entities.
	 *
	 * @Route("/", name="config_mailaccount")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities MailAccount[] */
		$entities = $em->getRepository('JeboehmLampcpCoreBundle:MailAccount')->findByDomain($this->_getSelectedDomain());

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a MailAccount entity.
	 *
	 * @Route("/{id}/show", name="config_mailaccount_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAccount */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAccount')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAccount entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Displays a form to create a new MailAccount entity.
	 *
	 * @Route("/new", name="config_mailaccount_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new MailAccount($this->_getSelectedDomain());
		$entity->setUsername($this->_getNewMailAccountName());

		$form = $this->createForm(new MailAccountType(), $entity);

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new MailAccount entity.
	 *
	 * @Route("/create", name="config_mailaccount_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:MailAccount:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new MailAccount($this->_getSelectedDomain());
		$entity->setUsername($this->_getNewMailAccountName());

		$form = $this->createForm(new MailAccountType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$entity->setPassword(md5($entity->getPassword()));

			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_mailaccount_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing MailAccount entity.
	 *
	 * @Route("/{id}/edit", name="config_mailaccount_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAccount */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAccount')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAccount entity.');
		}

		$editForm   = $this->createForm(new MailAccountType(true), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Edits an existing MailAccount entity.
	 *
	 * @Route("/{id}/update", name="config_mailaccount_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:MailAccount:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAccount */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAccount')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAccount entity.');
		}

		$oldPassword = $entity->getPassword();

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new MailAccountType(true), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			if(!$entity->getPassword()) {
				$entity->setPassword($oldPassword);
			} else {
				$entity->setPassword($entity->getPassword());
				$entity->setPassword(md5($entity->getPassword()));
			}

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_mailaccount_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'      => $entity,
									  'edit_form'   => $editForm->createView(),
									  'delete_form' => $deleteForm->createView(),
								 ));
	}

	/**
	 * Deletes a MailAccount entity.
	 *
	 * @Route("/{id}/delete", name="config_mailaccount_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity MailAccount */
			$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAccount')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find MailAccount entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_mailaccount'));
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

	/**
	 * Get repository
	 *
	 * @return MailAccountRepository
	 */
	protected function _getRepository() {
		return $this->getDoctrine()->getRepository('JeboehmLampcpCoreBundle:MailAccount');
	}

	/**
	 * Get new account name
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _getNewMailAccountName() {
		$prefix = $this->_getConfigService()->getParameter('mail.accountprefix');

		if(empty($prefix)) {
			throw new \Exception('Please set Mail Account Prefix in configuration!');
		}

		return $prefix . strval($this->_getRepository()->getFreeId());
	}
}
