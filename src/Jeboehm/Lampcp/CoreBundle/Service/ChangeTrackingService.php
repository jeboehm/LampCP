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

/**
 * Class ChangeTrackingService
 *
 * This class checks given repositories for changes
 * that are never than a given \DateTime
 *
 * @package Jeboehm\Lampcp\CoreBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ChangeTrackingService
{
    /** @var EntityManager */
    private $_em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * Get entities newer than $min.
     *
     * @param string    $repository
     * @param \DateTime $min
     *
     * @return mixed
     */
    public function findNewer($repository, \DateTime $min)
    {
        $qb = $this
            ->_getRepository($repository)
            ->createQueryBuilder('e');

        $qb
            ->where(
                $qb
                    ->expr()
                    ->gte('e.updated', '?1')
            )
            ->setParameter(1, $min);

        return $qb
            ->getQuery()
            ->execute();
    }

    /**
     * Get repository
     *
     * @param string $repository
     *
     * @return EntityRepository
     */
    protected function _getRepository($repository)
    {
        return $this
            ->_getEntityManager()
            ->getRepository($repository);
    }

    /**
     * Get entity manager
     *
     * @return EntityManager
     */
    protected function _getEntityManager()
    {
        return $this->_em;
    }
}
