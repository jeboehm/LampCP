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
use Jeboehm\Lampcp\CoreBundle\Entity\MailAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\MailForward;
use Jeboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jeboehm\Lampcp\CoreBundle\Form\MailAddressType;

/**
 * MailAddress controller.
 *
 * @Route("/config/mailaddress")
 */
class MailAddressController extends AbstractController implements ICrudController {
	/**
	 * Lists all MailAddress entities.
	 *
	 * @Route("/", name="config_mailaddress")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities MailAddress[] */
		$entities = $em
			->getRepository('JeboehmLampcpCoreBundle:MailAddress')
			->findByDomain($this->_getSelectedDomain());

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}

	/**
	 * Finds and displays a MailAddress entity.
	 *
	 * @Route("/{id}/show", name="config_mailaddress_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAddress entity.');
		}


		return $this->_getReturn(array(
									  'entity' => $entity,
								 ));
	}

	/**
	 * Displays a form to create a new MailAddress entity.
	 *
	 * @Route("/new", name="config_mailaddress_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new MailAddress($this->_getSelectedDomain());
		$form   = $this->createForm(new MailAddressType(), $entity);

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new MailAddress entity.
	 *
	 * @Route("/create", name="config_mailaddress_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:MailAddress:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity      = new MailAddress($this->_getSelectedDomain());
		$mailaccount = new MailAccount($entity->getDomain(), $entity);
		$entity->setMailaccount($mailaccount);

		$form = $this->createForm(new MailAddressType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_mailaddress_show', array('id' => $entity->getId())));
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Displays a form to edit an existing MailAddress entity.
	 *
	 * @Route("/{id}/edit", name="config_mailaddress_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAddress entity.');
		}

		$editForm = $this->createForm(new MailAddressType(true), $entity);

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Edits an existing MailAddress entity.
	 *
	 * @Route("/{id}/update", name="config_mailaddress_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:MailAddress:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAddress entity.');
		}

		$oldAccountPassword = $entity->getMailaccount()->getPassword();
		$oldForwards        = $entity->getMailforward();
		$editForm           = $this->createForm(new MailAddressType(true), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$newForwards = $entity->getMailforward();

			if(!$entity->getMailaccount()->getPassword()) {
				$entity->getMailaccount()->setPassword($oldAccountPassword);
			} else {
				$entity->getMailaccount()->setPassword(md5($entity->getMailaccount()->getPassword()));
			}

			/*
			 * Prüfen, ob MailForward gelöscht wurde
			 */
			foreach($oldForwards as $oldForward) {
				/** @var $oldForward MailForward */
				$delete = true;

				foreach($newForwards as $newForward) {
					/** @var $newForward MailForward */
					if($oldForward->getId() === $newForward->getId()) {
						$delete = false;
					}
				}

				if($delete) {
					$em->remove($oldForward);
					$entity->getMailforward()->removeElement($oldForward);
				}
			}

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_mailaddress_edit', array('id' => $id)));
		}

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Deletes a MailAddress entity.
	 *
	 * @Route("/{id}/delete", name="config_mailaddress_delete")
	 */
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity MailAddress */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:MailAddress')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find MailAddress entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_mailaddress'));
	}
}
