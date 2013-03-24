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
use Symfony\Component\HttpFoundation\Request;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntity as DoctrineEntity;
use Jeboehm\Lampcp\CoreBundle\Form\Model\ConfigEntity as FormEntity;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntityRepository;
use Jeboehm\Lampcp\CoreBundle\Form\Type\ConfigType;
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

/**
 * Config controller.
 *
 * @Route("/config/system")
 */
class SystemConfigController extends AbstractController {
    /**
     * Lists all Config entities.
     *
     * @Route("/", name="config_system")
     * @Template()
     */
    public function indexAction() {
        $entities = $this
            ->_getRepository()
            ->findAllOrdered();

        return array(
            'entities'    => $entities,
            'configtypes' => new ConfigTypes(),
        );
    }

    /**
     * Displays a form to edit SystemConfig entities.
     *
     * @Route("/edit", name="config_system_edit")
     * @Template()
     */
    public function editAction() {
        $entities = $this
            ->_getRepository()
            ->findAllOrdered();
        $entities = $this->_transformAllConfigEntities($entities);
        $form     = $this->createForm(new ConfigType(), array(
                                                             'configentities' => $entities,
                                                        ));

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edits SystemConfig entities
     *
     * @Route("/update", name="config_system_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:SystemConfig:edit.html.twig")
     */
    public function updateAction(Request $request) {
        $entities = $this
            ->_getRepository()
            ->findAllOrdered();
        $entities = $this->_transformAllConfigEntities($entities);
        $form     = $this->createForm(new ConfigType(), array(
                                                             'configentities' => $entities,
                                                        ));

        $form->bind($request);

        if ($form->isValid()) {
            foreach ($entities as $entity) {
                /** @var $entity FormEntity */
                $this
                    ->_getConfigService()
                    ->setParameter($entity->getFullName(), $entity->getValue());
            }

            return $this->redirect($this->generateUrl('config_system'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Get repository
     *
     * @return ConfigEntityRepository
     */
    private function _getRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:ConfigEntity');
    }

    /**
     * Remove doctrine bindings of the ConfigEntity
     *
     * @param DoctrineEntity $entity
     *
     * @return FormEntity
     */
    private function _transformConfigEntity(DoctrineEntity $entity) {
        $form = new FormEntity();
        $form
            ->setName($entity->getName())
            ->setValue($entity->getValue())
            ->setType($entity->getType())
            ->setConfiggroup($entity->getConfiggroup());

        return $form;
    }

    /**
     * Transform an array of ConfigEntity objects
     *
     * @param array $entities
     *
     * @return array
     */
    private function _transformAllConfigEntities(array $entities) {
        $new = array();

        foreach ($entities as $entity) {
            /** @var $entity DoctrineEntity */
            $new[] = $this->_transformConfigEntity($entity);
        }

        return $new;
    }
}
