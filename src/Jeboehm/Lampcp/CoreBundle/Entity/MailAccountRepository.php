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
 * MailAccountRepository
 */
class MailAccountRepository extends EntityRepository {
	/**
	 * Get next free id
	 *
	 * @return int
	 */
	public function getFreeId() {
		$qb = $this->createQueryBuilder('m');
		$qb->orderBy('m.id', 'desc');

		/** @var $arr MailAccount[] */
		$arr = $qb->getQuery()->execute();

		if(!is_array($arr) || count($arr) < 1) {
			return 1;
		}

		return intval($arr[0]->getId() + 1);
	}
}
