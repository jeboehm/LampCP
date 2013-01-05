<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BuilderChangeRepository
 */
class BuilderChangeRepository extends EntityRepository {
	/**
	 * Get BuilderChange by Array of Entitynames
	 *
	 * @param array $entitys
	 *
	 * @return BuilderChange[]
	 */
	public function getByEntitynamesArray(array $entitys) {
		$qb = $this->createQueryBuilder('b');
		$qb->where($qb->expr()->in('b.entityname', $entitys));

		return $qb->getQuery()->execute();
	}
}
