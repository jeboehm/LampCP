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
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabaseRepository;
use Jeboehm\Lampcp\CoreBundle\Form\Type\MysqlDatabaseType;

/**
 * Class MysqlDatabaseController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 *
 * @Route("/config/mysqldatabase")
 */
class MysqlDatabaseController extends AbstractController {
    /**
     * Lists all MysqlDatabase entities.
     *
     * @Route("/", name="config_mysqldatabase")
     * @Template()
     */
    public function indexAction() {
        /** @var $entities MysqlDatabase[] */
        $entities = $this
            ->_getRepository()
            ->findBy(array('domain' => $this->_getSelectedDomain()), array('name' => 'asc'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MysqlDatabase entity.
     *
     * @Route("/{entity}/show", name="config_mysqldatabase_show")
     * @Template()
     */
    public function showAction(MysqlDatabase $entity) {
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new MysqlDatabase entity.
     *
     * @Route("/new", name="config_mysqldatabase_new")
     * @Template()
     */
    public function newAction() {
        $entity = new MysqlDatabase($this->_getSelectedDomain());
        $entity->setName($this->_getNewDatabaseName());

        $form = $this->createForm(new MysqlDatabaseType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new MysqlDatabase entity.
     *
     * @Route("/create", name="config_mysqldatabase_create")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:MysqlDatabase:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new MysqlDatabase($this->_getSelectedDomain());
        $entity->setName($this->_getNewDatabaseName());

        $form = $this->createForm(new MysqlDatabaseType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->setPassword($this
                ->_getCryptService()
                ->encrypt($entity->getPassword()));

            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mysqldatabase_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MysqlDatabase entity.
     *
     * @Route("/{entity}/edit", name="config_mysqldatabase_edit")
     * @Template()
     */
    public function editAction(MysqlDatabase $entity) {
        $editForm = $this->createForm(new MysqlDatabaseType(), $entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing MysqlDatabase entity.
     *
     * @Route("/{entity}/update", name="config_mysqldatabase_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:MysqlDatabase:edit.html.twig")
     */
    public function updateAction(Request $request, MysqlDatabase $entity) {
        $oldPassword = $entity->getPassword();
        $editForm    = $this->createForm(new MysqlDatabaseType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if (!$entity->getPassword()) {
                $entity->setPassword($oldPassword);
            } else {
                $entity->setPassword($this
                    ->_getCryptService()
                    ->encrypt($entity->getPassword()));
            }

            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_mysqldatabase_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a MysqlDatabase entity.
     *
     * @Route("/{entity}/delete", name="config_mysqldatabase_delete")
     */
    public function deleteAction(MysqlDatabase $entity) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('config_mysqldatabase'));
    }

    /**
     * Return repository
     *
     * @return MysqlDatabaseRepository
     */
    private function _getRepository() {
        return $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JeboehmLampcpCoreBundle:MysqlDatabase');
    }

    /**
     * Get new database name
     *
     * @return string
     * @throws \Exception
     */
    private function _getNewDatabaseName() {
        $prefix = $this
            ->_getConfigService()
            ->getParameter('mysql.dbprefix');

        if (empty($prefix)) {
            throw new \Exception('Please set MySQL Database Prefix in configuration!');
        }

        return $prefix . strval($this
            ->_getRepository()
            ->getFreeId());
    }
}
