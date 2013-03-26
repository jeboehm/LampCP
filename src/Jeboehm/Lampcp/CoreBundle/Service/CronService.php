<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\CoreBundle\Entity\Cron;

/**
 * Class CronService
 *
 * Provides methods for handling cronjobs.
 * Only time tracking at the moment.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CronService {
    /** @var EntityManager */
    private $_em;

    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->_em = $em;
    }

    /**
     * Get entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function _getEntityManager() {
        return $this->_em;
    }

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function _getRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:Cron');
    }

    /**
     * Get or create Cron entity for $name
     *
     * @param string $name
     * @param bool   $create
     *
     * @return \Jeboehm\Lampcp\CoreBundle\Entity\Cron
     */
    protected function _getEntity($name, $create = true) {
        $entity = $this
            ->_getRepository()
            ->findOneBy(array('name' => $name));

        if ($entity === null && $create) {
            $entity = new Cron();
            $entity->setName($name);
        }

        return $entity;
    }

    /**
     * Update last run
     *
     * @param string $name
     */
    public function updateLastRun($name) {
        $entity = $this->_getEntity($name);

        $entity->setLastrun(new \DateTime());

        $this
            ->_getEntityManager()
            ->persist($entity);
        $this
            ->_getEntityManager()
            ->flush();
    }

    /**
     * Get last run
     *
     * @param string $name
     *
     * @return \DateTime|null
     */
    public function getLastRun($name) {
        $entity = $this->_getEntity($name, false);

        if (!$entity) {
            return null;
        } else {
            return $entity->getLastrun();
        }
    }
}
