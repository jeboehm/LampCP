<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\ValidationResult;

/**
 * Class PreFlightCheckService
 *
 * @package Jeboehm\Lampcp\SetupBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class PreFlightCheckService
{
    /** @var EntityManager */
    private $_em;
    /** @var ValidationResult */
    private $_result;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_result = new ValidationResult();
        $this
            ->getResult()
            ->setSuccessful(true);
    }

    /**
     * Get validation result.
     *
     * @return ValidationResult
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Executes all checks.
     */
    public function doChecks()
    {
        $this->checkDatabaseExists();
        $this->checkConfigInitialized();
        $this->checkUsersLoaded();
    }

    /**
     * Check, that the database exists.
     */
    public function checkDatabaseExists()
    {
        try {
            /** @var EntityRepository $repository */
            $repository = $this
                ->getEm()
                ->getRepository('JeboehmLampcpCoreBundle:User');
            $users      = $repository->findAll();
        } catch (\Exception $e) {
            $this->_addResultError('Database not created!');
        }
    }

    /**
     * Get Em
     *
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->_em;
    }

    /**
     * Set Em
     *
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
    }

    /**
     * Add error message to result.
     *
     * @param string $message
     */
    protected function _addResultError($message)
    {
        $this
            ->getResult()
            ->setSuccessful(false);

        $messages = $this
            ->getResult()
            ->getMessage();

        if (!is_array($messages)) {
            $messages = array();
        }

        $messages[] = $message;

        $this
            ->getResult()
            ->setMessage($messages);
    }

    /**
     * Check, that the config is initialized.
     */
    public function checkConfigInitialized()
    {
        try {
            /** @var EntityRepository $repository */
            $repository = $this
                ->getEm()
                ->getRepository('JeboehmLampcpCoreBundle:ConfigEntity');
            $config     = $repository->findAll();

            if (count($config) < 1) {
                $this->_addResultError('Configuration is not initialized!');
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Check, that some users are loaded.
     */
    public function checkUsersLoaded()
    {
        try {
            /** @var EntityRepository $repository */
            $repository = $this
                ->getEm()
                ->getRepository('JeboehmLampcpCoreBundle:User');
            $users      = $repository->findAll();

            if (count($users) < 1) {
                $this->_addResultError('Users are not loaded!');
            }
        } catch (\Exception $e) {
        }
    }
}
