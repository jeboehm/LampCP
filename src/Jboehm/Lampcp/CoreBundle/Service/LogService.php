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
	 * @param string                               $source
	 */
	protected function _save(Log $log, $message, $source) {
		$log->setMessage($message);
		$log->setSource($source);

		$this->_em->persist($log);
		$this->_em->flush();
	}

	/**
	 * New debug log entry
	 *
	 * @param string $message
	 * @param string $source
	 */
	public function debug($message, $source = '') {
		$log = new Log();
		$log->setType($log::TYPE_DEBUG);
		$this->_save($log, $message, $source);
	}

	/**
	 * New info log entry
	 *
	 * @param string $message
	 * @param string $source
	 */
	public function info($message, $source = '') {
		$log = new Log();
		$log->setType($log::TYPE_INFO);
		$this->_save($log, $message, $source);
	}

	/**
	 * New warning log entry
	 *
	 * @param string $message
	 * @param string $source
	 */
	public function warning($message, $source = '') {
		$log = new Log();
		$log->setType($log::TYPE_WARN);
		$this->_save($log, $message, $source);
	}

	/**
	 * New error log entry
	 *
	 * @param string $message
	 * @param string $source
	 */
	public function error($message, $source = '') {
		$log = new Log();
		$log->setType($log::TYPE_ERROR);
		$this->_save($log, $message, $source);
	}
}
