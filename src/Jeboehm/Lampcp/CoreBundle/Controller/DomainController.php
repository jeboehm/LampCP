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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Form\Type\DomainType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DomainController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 *
 * @Route("/config/domain")
 */
class DomainController extends AbstractController {
    /**
     * Lists all Domain entities.
     *
     * @Route("/", name="config_domain")
     * @Template()
     */
    public function indexAction() {
        /** @var $entities Domain[] */
        $entities = $this
            ->_getRepository()
            ->findBy(array(), array('domain' => 'asc'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Domain entity.
     *
     * @Route("/{entity}/show", name="config_domain_show")
     * @Template()
     */
    public function showAction(Domain $entity) {
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Domain entity.
     *
     * @Route("/new", name="config_domain_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Domain();
        $entity->setPath('(auto)');
        $form = $this->createForm(new DomainType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Domain entity.
     *
     * @Route("/create", name="config_domain_create")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Domain:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Domain();
        $form   = $this->createForm(new DomainType(), $entity);
        $form->bind($request);
        $entity->setPath($this->_getSystemWebPath() . '/' . $entity->getDomain());

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_domain_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Domain entity.
     *
     * @Route("/{entity}/edit", name="config_domain_edit")
     * @Template()
     */
    public function editAction(Domain $entity) {
        $editForm = $this->createForm(new DomainType(), $entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Domain entity.
     *
     * @Route("/{entity}/update", name="config_domain_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Domain:edit.html.twig")
     */
    public function updateAction(Request $request, Domain $entity) {
        $editForm = $this->createForm(new DomainType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_domain_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Domain entity.
     *
     * @Route("/{entity}/delete", name="config_domain_delete")
     */
    public function deleteAction(Domain $entity) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('config_domain'));
    }

    /**
     * Get configured system web path
     *
     * @return string
     */
    private function _getSystemWebPath() {
        return $this
            ->_getConfigService()
            ->getParameter('apache.pathwww');
    }

    /**
     * Get repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function _getRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain');
    }
}
