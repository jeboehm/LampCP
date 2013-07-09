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

use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jeboehm\Lampcp\CoreBundle\Entity\Admin;
use Jeboehm\Lampcp\CoreBundle\Form\Model\AdminRoles;
use Jeboehm\Lampcp\CoreBundle\Form\Type\AdminType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 *
 * @Route("/config/admin")
 */
class AdminController extends AbstractController {
    /**
     * Lists all Admin entities.
     *
     * @Route("/", name="config_admin")
     * @Template()
     */
    public function indexAction() {
        $entities = $this
            ->_getUserManager()
            ->findUsers();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays an Admin entity.
     *
     * @Route("/{entity}/show", name="config_admin_show")
     * @Template()
     */
    public function showAction(Admin $entity) {
        return array(
            'entity'    => $entity,
            'roleTrans' => AdminRoles::$roles,
        );
    }

    /**
     * Displays a form to create a new Admin entity.
     *
     * @Route("/new", name="config_admin_new")
     * @Template()
     */
    public function newAction() {
        $entity = $this
            ->_getUserManager()
            ->createUser();
        $entity->setEnabled(true);

        $form = $this->createForm(new AdminType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Admin entity.
     *
     * @Route("/create", name="config_admin_create")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Admin:new.html.twig")
     */
    public function createAction(Request $request) {
        /** @var $entity Admin */
        $entity = $this
            ->_getUserManager()
            ->createUser();
        $form   = $this->createForm(new AdminType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $this
                ->_getUserManager()
                ->updateUser($entity);

            return $this->redirect($this->generateUrl('config_admin_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Admin entity.
     *
     * @Route("/{entity}/edit", name="config_admin_edit")
     * @Template()
     */
    public function editAction(Admin $entity) {
        $editForm = $this->createForm(new AdminType(), $entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Admin entity.
     *
     * @Route("/{entity}/update", name="config_admin_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Protection:edit.html.twig")
     */
    public function updateAction(Request $request, Admin $entity) {
        $editForm = $this->createForm(new AdminType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $this
                ->_getUserManager()
                ->updateUser($entity);

            return $this->redirect($this->generateUrl('config_admin_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes an Admin entity.
     *
     * @Route("/{entity}/delete", name="config_admin_delete")
     */
    public function deleteAction(Admin $entity) {
        $this
            ->_getUserManager()
            ->deleteUser($entity);

        return $this->redirect($this->generateUrl('config_admin'));
    }

    /**
     * Get user manager
     *
     * @return UserManagerInterface
     */
    private function _getUserManager() {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        return $userManager;
    }
}
