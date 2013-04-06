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
 * Class CertificateController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
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
        /** @var $entities Certificate[] */
        $entities = $this
            ->_getRepository()
            ->findBy(array(), array('name' => 'asc'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Certificate entity.
     *
     * @Route("/{entity}/show", name="config_certificate_show")
     * @Template()
     */
    public function showAction(Certificate $entity) {
        try {
            $privKey = $entity->getCertificateKeyFile();

            if (!empty($privKey)) {
                $entity->setCertificateKeyFile($this
                    ->_getCryptService()
                    ->decrypt($privKey));
            }
        } catch (\Exception $e) {
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

        if ($form->isValid()) {
            $privKey = $entity->getCertificateKeyFile();

            if (!empty($privKey)) {
                $entity->setCertificateKeyFile($this
                    ->_getCryptService()
                    ->encrypt($privKey));
            }

            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_certificate_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Certificate entity.
     *
     * @Route("/{entity}/edit", name="config_certificate_edit")
     * @Template()
     */
    public function editAction(Certificate $entity) {
        try {
            $privKey = $entity->getCertificateKeyFile();

            if (!empty($privKey)) {
                $entity->setCertificateKeyFile($this
                    ->_getCryptService()
                    ->decrypt($privKey));
            }
        } catch (\Exception $e) {
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
     * @Route("/{entity}/update", name="config_certificate_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Certificate:edit.html.twig")
     */
    public function updateAction(Request $request, Certificate $entity) {
        $editForm = $this->createForm(new CertificateType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $privKey = $this
                ->_getCryptService()
                ->encrypt($entity->getCertificateKeyFile());
            $entity->setCertificateKeyFile($privKey);

            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_certificate_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Certificate entity.
     *
     * @Route("/{entity}/delete", name="config_certificate_delete")
     */
    public function deleteAction(Certificate $entity) {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('config_certificate'));
    }

    /**
     * Get repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function _getRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Certificate');
    }
}
