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
use Jeboehm\Lampcp\CoreBundle\Form\Type\MailAddressType;

/**
 * Class MailAddressController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 *
 * @Route("/config/mailaddress")
 */
class MailAddressController extends AbstractController {
    /**
     * Lists all MailAddress entities.
     *
     * @Route("/", name="config_mailaddress")
     * @Template()
     */
    public function indexAction() {
        /** @var $entities MailAddress[] */
        $entities = $this
            ->_getRepository()
            ->findBy(array('domain' => $this->_getSelectedDomain()), array('address' => 'asc'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MailAddress entity.
     *
     * @Route("/{entity}/show", name="config_mailaddress_show")
     * @Template()
     */
    public function showAction(MailAddress $entity) {
        return array(
            'entity' => $entity,
        );
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

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new MailAddress entity.
     *
     * @Route("/create", name="config_mailaddress_create")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:MailAddress:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new MailAddress($this->_getSelectedDomain());
        $form   = $this->createForm(new MailAddressType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaddress_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MailAddress entity.
     *
     * @Route("/{entity}/edit", name="config_mailaddress_edit")
     * @Template()
     */
    public function editAction(MailAddress $entity) {
        $editForm = $this->createForm(new MailAddressType(), $entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing MailAddress entity.
     *
     * @Route("/{entity}/update", name="config_mailaddress_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:MailAddress:edit.html.twig")
     */
    public function updateAction(Request $request, MailAddress $entity) {
        $oldForwards = $entity->getMailforward();
        $editForm    = $this->createForm(new MailAddressType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em          = $this
                ->getDoctrine()
                ->getManager();
            $newForwards = $entity->getMailforward();

            /*
             * Prüfen, ob MailForward gelöscht wurde
             */
            foreach ($oldForwards as $oldForward) {
                /** @var $oldForward MailForward */
                $delete = true;

                foreach ($newForwards as $newForward) {
                    /** @var $newForward MailForward */
                    if ($oldForward->getId() === $newForward->getId()) {
                        $delete = false;
                    }
                }

                if ($delete) {
                    $em->remove($oldForward);
                    $entity
                        ->getMailforward()
                        ->removeElement($oldForward);
                }
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaddress_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a MailAddress entity.
     *
     * @Route("/{entity}/delete", name="config_mailaddress_delete")
     */
    public function deleteAction(MailAddress $entity) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $em->remove($entity->getMailaccount());

        foreach ($entity->getMailforward() as $forward) {
            $em->remove($forward);
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('config_mailaddress'));
    }

    /**
     * Get repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function _getRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:MailAddress');
    }
}
