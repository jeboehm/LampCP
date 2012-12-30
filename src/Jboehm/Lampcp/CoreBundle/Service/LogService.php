<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Jboehm\Lampcp\CoreBundle\Entity\Log;

class LogService {
	/** @var EntityManager */
	protected $_em;

	/**
	 * Konstruktor
	 *
	 * @param EntityManager $em
	 */
	public function __construct($em) {
		$this->_em = $em;
	}

	/**
	 * Save log
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Log $log
	 * @param string                               $message
	 */
	protected function _save(Log $log, $message) {
		$log->setMessage($message);

		$this->_em->persist($log);
		$this->_em->flush();
	}

	/**
	 * New debug log entry
	 *
	 * @param string $message
	 */
	public function debug($message) {
		$log = new Log();
		$log->setType($log::TYPE_DEBUG);
		$this->_save($log, $message);
	}

	/**
	 * New info log entry
	 *
	 * @param string $message
	 */
	public function info($message) {
		$log = new Log();
		$log->setType($log::TYPE_INFO);
		$this->_save($log, $message);
	}

	/**
	 * New warning log entry
	 *
	 * @param string $message
	 */
	public function warning($message) {
		$log = new Log();
		$log->setType($log::TYPE_WARN);
		$this->_save($log, $message);
	}

	/**
	 * New error log entry
	 *
	 * @param string $message
	 */
	public function error($message) {
		$log = new Log();
		$log->setType($log::TYPE_ERROR);
		$this->_save($log, $message);
	}
}
