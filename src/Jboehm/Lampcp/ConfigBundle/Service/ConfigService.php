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
use Doctrine\ORM\EntityManager;
use Jboehm\Lampcp\CoreBundle\Service\CryptService;
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntityRepository;
use Jboehm\Lampcp\ConfigBundle\Exception\ConfigEntityNotFoundException;

class ConfigService {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Symfony\Bridge\Monolog\Logger */
	private $_log;

	/** @var \Jboehm\Lampcp\CoreBundle\Service\CryptService */
	private $_cs;

	/**
	 * Konstruktor
	 *
	 * @param \Doctrine\ORM\EntityManager                    $em
	 * @param \Symfony\Bridge\Monolog\Logger                 $log
	 * @param \Jboehm\Lampcp\CoreBundle\Service\CryptService $cs
	 */
	public function __construct(EntityManager $em, Logger $log, CryptService $cs) {
		$this->_em  = $em;
		$this->_log = $log;
		$this->_cs  = $cs;
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
					$retval = $this->_cs->decrypt($entval);
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
		$entval = $entity->getValue();
		$newval = '';

		if($value === $entval) {
			return;
		}

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
}
