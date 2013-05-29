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
use Jeboehm\Lampcp\CoreBundle\Entity\AbstractEntity;
use Jeboehm\Lampcp\CoreBundle\Entity\Cron;

/**
 * Class BuilderNotifierService
 *
 * Search for a responsible configbuilder for the given repository
 * and force the execution of it.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class BuilderNotifierService
{
    /** @var ConfigBuilderCommandCollector */
    private $builderCollector;
    /** @var EntityManager */
    private $entityManager;

    /**
     * Search for responsible configbuilders by entity
     * and notify them.
     *
     * @param AbstractEntity $entity
     */
    public function notifyBuilder(AbstractEntity $entity)
    {
        $changedEntityName = get_class($entity);
        $relevantBuilders  = array();

        foreach ($this
                     ->getBuilderCollector()
                     ->getBuilders() as $builder) {
            foreach ($builder::getListenEntities() as $listenEntityName) {
                $listenEntityName = $this->getEntityClassName($listenEntityName);

                if ($changedEntityName === $listenEntityName) {
                    if (!in_array($builder::getCommandName(), $relevantBuilders)) {
                        $relevantBuilders[] = $builder::getCommandName();
                    }

                    break;
                }
            }
        }

        foreach ($relevantBuilders as $builderName) {
            $this->forceBuilder($builderName);
        }
    }

    /**
     * Get BuilderCollector.
     *
     * @return ConfigBuilderCommandCollector
     */
    protected function getBuilderCollector()
    {
        return $this->builderCollector;
    }

    /**
     * Set BuilderCollector.
     *
     * @param ConfigBuilderCommandCollector $builderCollector
     *
     * @return $this
     */
    public function setBuilderCollector(ConfigBuilderCommandCollector $builderCollector)
    {
        $this->builderCollector = $builderCollector;

        return $this;
    }

    /**
     * Get the FQCN of an entity.
     * Supports aliases.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getEntityClassName($name)
    {
        return $this
            ->getEntityManager()
            ->getClassMetadata($name)
            ->getName();
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set EntityManager.
     *
     * @param EntityManager $entityManager
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Set force=true to config builder's cronjob.
     *
     * @param string $builderName
     *
     * @return bool
     */
    protected function forceBuilder($builderName)
    {
        $cron = $this->getBuilderCron($builderName);

        if (!$cron) {
            // Should run automatically.
            return true;
        }

        $cron->setForce(true);

        $this
            ->getEntityManager()
            ->flush();

        return true;
    }

    /**
     * Get builder's cron entity.
     *
     * @param string $builderName
     *
     * @return Cron|null
     */
    protected function getBuilderCron($builderName)
    {
        /** @var EntityRepository $repository */
        $repository = $this
            ->getEntityManager()
            ->getRepository('JeboehmLampcpCoreBundle:Cron');

        $cron = $repository->findOneBy(
            array(
                 'name' => $builderName,
            )
        );

        return $cron;
    }
}
