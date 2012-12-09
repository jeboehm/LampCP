<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jboehm\Lampcp\CoreBundle\Form\MailAccountType;

/**
 * MailAccount controller.
 *
 * @Route("/config/mailaccount")
 */
class MailAccountController extends Controller
{
    /**
     * Lists all MailAccount entities.
     *
     * @Route("/", name="config_mailaccount")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JboehmLampcpCoreBundle:MailAccount')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MailAccount entity.
     *
     * @Route("/{id}/show", name="config_mailaccount_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAccount entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new MailAccount entity.
     *
     * @Route("/new", name="config_mailaccount_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MailAccount();
        $form   = $this->createForm(new MailAccountType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new MailAccount entity.
     *
     * @Route("/create", name="config_mailaccount_create")
     * @Method("POST")
     * @Template("JboehmLampcpCoreBundle:MailAccount:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new MailAccount();
        $form = $this->createForm(new MailAccountType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaccount_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MailAccount entity.
     *
     * @Route("/{id}/edit", name="config_mailaccount_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAccount entity.');
        }

        $editForm = $this->createForm(new MailAccountType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MailAccount entity.
     *
     * @Route("/{id}/update", name="config_mailaccount_update")
     * @Method("POST")
     * @Template("JboehmLampcpCoreBundle:MailAccount:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAccount entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MailAccountType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaccount_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MailAccount entity.
     *
     * @Route("/{id}/delete", name="config_mailaccount_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAccount')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MailAccount entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('config_mailaccount'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
