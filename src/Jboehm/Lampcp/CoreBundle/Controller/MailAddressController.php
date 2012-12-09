<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\MailAddress;
use Jboehm\Lampcp\CoreBundle\Form\MailAddressType;

/**
 * MailAddress controller.
 *
 * @Route("/config/mailaddress")
 */
class MailAddressController extends Controller
{
    /**
     * Lists all MailAddress entities.
     *
     * @Route("/", name="config_mailaddress")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JboehmLampcpCoreBundle:MailAddress')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MailAddress entity.
     *
     * @Route("/{id}/show", name="config_mailaddress_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAddress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAddress entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new MailAddress entity.
     *
     * @Route("/new", name="config_mailaddress_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MailAddress();
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
     * @Template("JboehmLampcpCoreBundle:MailAddress:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new MailAddress();
        $form = $this->createForm(new MailAddressType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaddress_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MailAddress entity.
     *
     * @Route("/{id}/edit", name="config_mailaddress_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAddress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAddress entity.');
        }

        $editForm = $this->createForm(new MailAddressType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MailAddress entity.
     *
     * @Route("/{id}/update", name="config_mailaddress_update")
     * @Method("POST")
     * @Template("JboehmLampcpCoreBundle:MailAddress:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAddress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MailAddress entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MailAddressType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mailaddress_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MailAddress entity.
     *
     * @Route("/{id}/delete", name="config_mailaddress_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JboehmLampcpCoreBundle:MailAddress')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MailAddress entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('config_mailaddress'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
