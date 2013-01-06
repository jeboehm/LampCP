<?php

namespace Jboehm\Lampcp\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MysqlDatabaseRepository
 */
class MysqlDatabaseRepository extends EntityRepository {
	/**
	 * Get next free id
	 *
	 * @return int
	 */
	public function getFreeId() {
		$qb = $this->createQueryBuilder('m');
		$qb->orderBy('m.id', 'desc');

		/** @var $arr MysqlDatabase[] */
		$arr = $qb->getQuery()->execute();

		if(!is_array($arr) || count($arr) < 1) {
			return 1;
		}

		return intval($arr[0]->getId() + 1);
	}
}
