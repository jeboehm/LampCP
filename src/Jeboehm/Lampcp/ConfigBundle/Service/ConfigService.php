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

class ConfigService {
    /** @var \Doctrine\ORM\EntityManager */
    private $_em;

    /** @var \Jeboehm\Lampcp\CoreBundle\Service\CryptService */
    private $_cs;

    /**
     * Konstruktor
     *
     * @param \Doctrine\ORM\EntityManager                     $em
     * @param \Jeboehm\Lampcp\CoreBundle\Service\CryptService $cs
     */
    public function __construct(EntityManager $em, CryptService $cs) {
        $this->_em = $em;
        $this->_cs = $cs;
    }

    /**
     * Get config entity repository
     *
     * @return ConfigEntityRepository
     */
    protected function _getConfigEntityRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:ConfigEntity');
    }

    /**
     * Get entity
     *
     * @param string $name
     *
     * @return ConfigEntity
     * @throws ConfigEntityNotFoundException
     */
    protected function _getEntity($name) {
        $nameArr = explode('.', $name, 2);
        $group   = $nameArr[0];
        $conf    = $nameArr[1];
        $entity  = $this
            ->_getConfigEntityRepository()
            ->findOneByNameAndGroup($conf, $group);

        if (!$entity) {
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
     * Set config parameter
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
            $this->_em->persist($entity);
            $this->_em->flush();
        }
    }
}
