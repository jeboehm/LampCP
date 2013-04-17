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
use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\Cron;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;

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

    /** @var ChangeTrackingService */
    private $_cs;

    /**
     * Constructor.
     *
     * @param EntityManager         $em
     * @param ChangeTrackingService $cs
     */
    public function __construct(EntityManager $em, ChangeTrackingService $cs) {
        $this->_em = $em;
        $this->_cs = $cs;
    }

    /**
     * Get entity manager.
     *
     * @return EntityManager
     */
    protected function _getEntityManager() {
        return $this->_em;
    }

    /**
     * Get repository.
     *
     * @return EntityRepository
     */
    protected function _getRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:Cron');
    }

    /**
     * Get or create Cron entity for $name.
     *
     * @param string $name
     * @param bool   $create
     *
     * @return Cron
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
     * Update last run.
     *
     * @param string $name
     *
     * @return \DateTime
     */
    public function updateLastRun($name) {
        $entity = $this->_getEntity($name);
        $time   = new \DateTime();

        $entity->setLastrun($time);

        $this
            ->_getEntityManager()
            ->persist($entity);
        $this
            ->_getEntityManager()
            ->flush();

        return $time;
    }

    /**
     * Get last run.
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

    /**
     * Check for changed entities since last run.
     *
     * @param string $name
     * @param array  $entities
     *
     * @return bool
     */
    public function checkEntitiesChanged($name, array $entities) {
        $last = $this->getLastRun($name);

        if ($last === null) {
            return true;
        } else {
            foreach ($entities as $entity) {
                $result = $this->_cs->findNewer($entity, $last);

                if (count($result) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}
