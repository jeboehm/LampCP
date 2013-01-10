<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ConfigBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Exception;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Jboehm\Lampcp\CoreBundle\Service\CryptService;
use Jboehm\Lampcp\ConfigBundle\Form\ConfigType;
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntityRepository;
use Jboehm\Lampcp\ConfigBundle\Exception\ConfigEntityNotFoundException;
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

class ConfigService {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Symfony\Bridge\Monolog\Logger */
	private $_log;

	/** @var \Jboehm\Lampcp\CoreBundle\Service\CryptService */
	private $_cs;

	/** @var \Symfony\Component\Form\FormFactory */
	private $_form;

	/**
	 * Konstruktor
	 *
	 * @param \Doctrine\ORM\EntityManager                    $em
	 * @param \Symfony\Bridge\Monolog\Logger                 $log
	 * @param \Jboehm\Lampcp\CoreBundle\Service\CryptService $cs
	 * @param \Symfony\Component\Form\FormFactory            $form
	 */
	public function __construct(EntityManager $em,
								Logger $log,
								CryptService $cs,
								FormFactory $form) {
		$this->_em   = $em;
		$this->_log  = $log;
		$this->_cs   = $cs;
		$this->_form = $form;
	}

	/**
	 * Get config group repository
	 *
	 * @return \Doctrine\ORM\EntityRepository
	 */
	protected function _getConfigGroupRepository() {
		return $this->_em->getRepository('JboehmLampcpConfigBundle:ConfigGroup');
	}

	/**
	 * Get config entity repository
	 *
	 * @return ConfigEntityRepository
	 */
	protected function _getConfigEntityRepository() {
		return $this->_em->getRepository('JboehmLampcpConfigBundle:ConfigEntity');
	}

	/**
	 * Get entity
	 *
	 * @param string $name
	 *
	 * @return \Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntity
	 * @throws \Jboehm\Lampcp\ConfigBundle\Exception\ConfigEntityNotFoundException
	 */
	protected function _getEntity($name) {
		$nameArr = explode('.', $name, 2);
		$group   = $nameArr[0];
		$conf    = $nameArr[1];
		$entity  = $this->_getConfigEntityRepository()->findOneByNameAndGroup($conf, $group);

		if(!$entity) {
			throw new ConfigEntityNotFoundException();
		}

		return $entity;
	}

	/**
	 * Get config parameter
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getParameter($name) {
		$entity = $this->_getEntity($name);
		$retval = '';
		$entval = $entity->getValue();

		if(!empty($entval)) {
			switch($entity->getType()) {
				case $entity::TYPE_PASSWORD:
					try {
						$retval = $this->_cs->decrypt($entval);
					} catch(\Exception $e) {
						$retval = '';
					}
					break;

				default:
					$retval = $entval;
			}
		}

		return $retval;
	}

	/**
	 * Set config parameter
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setParameter($name, $value) {
		$entity = $this->_getEntity($name);
		$newval = '';

		switch($entity->getType()) {
			case $entity::TYPE_PASSWORD:
				if(!empty($value)) {
					$newval = $this->_cs->encrypt($value);
				}
				break;

			case $entity::TYPE_BOOL:
				$newval = strval((bool)$value);
				break;

			default:
				$newval = $value;
		}

		if($newval !== $entity->getValue()) {
			$entity->setValue($newval);
			$this->_em->persist($entity);
			$this->_em->flush();
		}
	}

	/**
	 * Get config form
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm() {
		/** @var $entities ConfigEntity[] */
		$entities = $this->_getConfigEntityRepository()->findAll();

		foreach($entities as $entity) {
			$name = $entity->getConfiggroup()->getName() . '.' . $entity->getName();
			$entity->setValue($this->getParameter($name));
		}

		$form = $this->_form->create(new ConfigType(), array(
															'configentity' => $entities,
													   ));

		return $form;
	}
}
