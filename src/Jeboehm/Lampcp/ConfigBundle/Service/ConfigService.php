<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntityRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntity;
use Jeboehm\Lampcp\ConfigBundle\Exception\ConfigEntityNotFoundException;
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

/**
 * Class ConfigService
 *
 * Methods for handling the configuration.
 *
 * @package Jeboehm\Lampcp\ConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigService {
    /** @var EntityManager */
    private $_em;

    /** @var CryptService */
    private $_cs;

    /**
     * Set Crypt Service.
     *
     * @param CryptService $cs
     *
     * @return ConfigService
     */
    public function setCs(CryptService $cs) {
        $this->_cs = $cs;

        return $this;
    }

    /**
     * Get Crypt Service.
     *
     * @return CryptService
     */
    public function getCs() {
        return $this->_cs;
    }

    /**
     * Set Entity Manager.
     *
     * @param EntityManager $em
     *
     * @return ConfigService
     */
    public function setEm(EntityManager $em) {
        $this->_em = $em;

        return $this;
    }

    /**
     * Get Entity Manager.
     *
     * @return EntityManager
     */
    public function getEm() {
        return $this->_em;
    }

    /**
     * Get config entity repository.
     *
     * @return ConfigEntityRepository
     */
    protected function _getConfigEntityRepository() {
        return $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:ConfigEntity');
    }

    /**
     * Get entity.
     *
     * @param string $name
     *
     * @return ConfigEntity
     * @throws ConfigEntityNotFoundException
     */
    protected function _getEntity($name) {
        $nameArr = explode('.', $name, 2);

        if (count($nameArr) !== 2) {
            throw new ConfigEntityNotFoundException();
        }

        $group  = $nameArr[0];
        $conf   = $nameArr[1];
        $entity = $this
            ->_getConfigEntityRepository()
            ->findOneByNameAndGroup($conf, $group);

        if (!$entity) {
            throw new ConfigEntityNotFoundException();
        }

        return $entity;
    }

    /**
     * Get config parameter.
     *
     * @param string $name
     *
     * @return string
     */
    public function getParameter($name) {
        $entity = $this->_getEntity($name);
        $retval = '';
        $entval = $entity->getValue();

        if (!empty($entval)) {
            switch ($entity->getType()) {
                case ConfigTypes::TYPE_PASSWORD:
                    try {
                        $retval = $this->_cs->decrypt($entval);
                    } catch (\Exception $e) {
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
     * Set config parameter.
     *
     * @param string $name
     * @param string $value
     */
    public function setParameter($name, $value) {
        $entity = $this->_getEntity($name);
        $newval = '';

        switch ($entity->getType()) {
            case ConfigTypes::TYPE_PASSWORD:
                if (!empty($value)) {
                    $newval = $this->_cs->encrypt($value);
                }
                break;

            case ConfigTypes::TYPE_BOOL:
                $newval = strval((bool)$value);
                break;

            default:
                $newval = $value;
        }

        if ($newval != $entity->getValue()) {
            $entity->setValue($newval);
            $this
                ->getEm()
                ->persist($entity);
            $this
                ->getEm()
                ->flush();
        }
    }
}
