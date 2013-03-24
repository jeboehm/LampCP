<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ConfigEntityRepository
 */
class ConfigEntityRepository extends EntityRepository {
    /**
     * Get Entity by name and group
     *
     * @param string $name
     * @param string $group
     *
     * @return ConfigEntity
     */
    public function findOneByNameAndGroup($name, $group) {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->leftJoin('e.configgroup', 'g')
            ->where('e.name = ?1')
            ->andWhere('g.name = ?2')
            ->setParameter(1, $name)
            ->setParameter(2, $group);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get all entities, ordered by groupname
     *
     * @return ConfigEntity[]
     */
    public function findAllOrdered() {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.configgroup', 'g')
            ->orderBy('g.name', 'asc')
            ->addOrderBy('c.id', 'asc');

        return $qb
            ->getQuery()
            ->getResult();
    }
}
