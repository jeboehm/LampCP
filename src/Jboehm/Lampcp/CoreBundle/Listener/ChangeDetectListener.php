<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Listener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Jboehm\Lampcp\CoreBundle\Entity\BuilderChange;

class ChangeDetectListener {
	/**
	 * Log Change
	 *
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 * @param int                                    $type
	 */
	protected function _logChange(LifecycleEventArgs $args, $type) {
		$em     = $args->getEntityManager();
		$entity = $args->getEntity();
		$change = new BuilderChange();

		$change
			->setEntityname(get_class($entity))
			->setMethod($type);

		if(!($entity instanceof BuilderChange)) {
			$em->persist($change);
			$em->flush();
		}
	}

	/**
	 * Doctrine Event: postPersist
	 *
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$this->_logChange($args, BuilderChange::METHOD_CREATE);
	}

	/**
	 * Doctrine Event: preRemove
	 *
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$this->_logChange($args, BuilderChange::METHOD_REMOVE);
	}

	/**
	 * Doctrine Event: postUpdate
	 *
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args) {
		$this->_logChange($args, BuilderChange::METHOD_UPDATE);
	}
}
