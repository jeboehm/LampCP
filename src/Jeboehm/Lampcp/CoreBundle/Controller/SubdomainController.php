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
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Form\Type\SubdomainType;

/**
 * Subdomain controller.
 *
 * @Route("/config/subdomain")
 */
class SubdomainController extends AbstractController implements ICrudController {
	/**
	 * Lists all Subdomain entities.
	 *
	 * @Route("/", name="config_subdomain")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		/** @var $entities Subdomain[] */
		$entities = $em
			->getRepository('JeboehmLampcpCoreBundle:Subdomain')
			->findByDomain($this->_getSelectedDomain(), array('subdomain' => 'asc'));

		return array(
			'entities' => $entities,
		);
	}

	/**
	 * Finds and displays a Subdomain entity.
	 *
	 * @Route("/{id}/show", name="config_subdomain_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}


		return array(
			'entity' => $entity,
		);
	}

	/**
	 * Displays a form to create a new Subdomain entity.
	 *
	 * @Route("/new", name="config_subdomain_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Subdomain($this->_getSelectedDomain());
		$form   = $this->createForm(new SubdomainType(), $entity);

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Creates a new Subdomain entity.
	 *
	 * @Route("/create", name="config_subdomain_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Subdomain:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Subdomain($this->_getSelectedDomain());
		$form   = $this->createForm(new SubdomainType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_subdomain_show', array('id' => $entity->getId())));
		}

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Displays a form to edit an existing Subdomain entity.
	 *
	 * @Route("/{id}/edit", name="config_subdomain_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$editForm = $this->createForm(new SubdomainType(), $entity);

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Edits an existing Subdomain entity.
	 *
	 * @Route("/{id}/update", name="config_subdomain_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Subdomain:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$editForm = $this->createForm(new SubdomainType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_subdomain_edit', array('id' => $id)));
		}

		return array(
			'entity'    => $entity,
			'edit_form' => $editForm->createView(),
		);
	}

	/**
	 * Deletes a Subdomain entity.
	 *
	 * @Route("/{id}/delete", name="config_subdomain_delete")
	 */
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_subdomain'));
	}
}
