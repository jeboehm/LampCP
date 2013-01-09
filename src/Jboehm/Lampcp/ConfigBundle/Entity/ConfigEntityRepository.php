<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ConfigBundle\Entity;

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
			->from('\Jboehm\Lampcp\ConfigBundle\Entity\ConfigGroup', 'g')
			->where('e.name = ?1')
			->andWhere('g.name = ?2')
			->setParameter(1, $name)
			->setParameter(2, $group);

		return $qb->getQuery()->getOneOrNullResult();
	}
}
