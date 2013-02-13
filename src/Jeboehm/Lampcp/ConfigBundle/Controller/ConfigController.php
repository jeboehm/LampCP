<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Controller;

use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Config controller.
 *
 * @Route("/config/system", service="jeboehm_lampcp_config_configcontroller")
 */
class ConfigController extends ContainerAware {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService */
	private $_config;

	/** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
	private $_router;

	/**
	 * Konstruktor
	 *
	 * @param \Doctrine\ORM\EntityManager                        $em
	 * @param \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService $config
	 * @param \Symfony\Bundle\FrameworkBundle\Routing\Router     $router
	 */
	public function __construct(EntityManager $em, ConfigService $config, Router $router) {
		$this->_em     = $em;
		$this->_config = $config;
		$this->_router = $router;
	}

	/**
	 * Lists all Config entities.
	 *
	 * @Route("/", name="systemconfig")
	 * @Template()
	 */
	public function indexAction() {
		$groups = $this->_em->getRepository('JeboehmLampcpConfigBundle:ConfigGroup')->findAll();

		return array(
			'groups' => $groups,
		);
	}

	/**
	 * Shows all Config entities in edit form.
	 *
	 * @Route("/edit", name="systemconfig_edit")
	 * @Template()
	 */
	public function editAction() {
		return array(
			'form' => $this->_config->getForm()->createView()
		);
	}

	/**
	 * Update configuration
	 *
	 * @Route("/update", name="systemconfig_update")
	 */
	public function updateAction(Request $request) {
		$form = $this->_config->getForm();
		$form->bind($request);

		if($form->isValid()) {
			$formdata = $form->getData();

			foreach($formdata['configentity'] as $entity) {
				/** @var $entity ConfigEntity */
				$name = $entity->getConfiggroup()->getName() . '.' . $entity->getName();
				$this->_config->setParameter($name, $entity->getValue());
			}
		}

		return new RedirectResponse($this->_router->generate('systemconfig'));
	}
}
