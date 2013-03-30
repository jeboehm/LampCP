<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\MysqlBundle\Model\MysqlDatabaseModel;
use Jeboehm\Lampcp\MysqlBundle\Model\MysqlUserModel;
use Symfony\Bridge\Monolog\Logger;
use Jeboehm\Lampcp\MysqlBundle\Service\MysqlAdminService;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabaseRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;

/**
 * Class MysqlSynchronizerService
 *
 * One way synchronization LampCP -> MySQLd
 * - Create new users
 * - Delete old users
 * - Create new databases
 * - Delete old databases
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlSynchronizerService {
    /** @var EntityManager */
    protected $_em;

    /** @var Logger */
    protected $_logger;

    /** @var MysqlAdminService */
    protected $_mysqladmin;

    /** @var ConfigService */
    protected $_systemconfig;

    /** @var CryptService */
    protected $_cryptservice;

    /**
     * Konstruktor
     *
     * @param EntityManager                                      $em
     * @param \Symfony\Bridge\Monolog\Logger                     $logger
     * @param MysqlAdminService                                  $mysqladmin
     * @param \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService $systemconfig
     * @param \Jeboehm\Lampcp\CoreBundle\Service\CryptService    $cryptservice
     */
    public function __construct(EntityManager $em, Logger $logger, MysqlAdminService $mysqladmin, ConfigService $systemconfig, CryptService $cryptservice) {
        $this->_em           = $em;
        $this->_logger       = $logger;
        $this->_mysqladmin   = $mysqladmin;
        $this->_systemconfig = $systemconfig;
        $this->_cryptservice = $cryptservice;
    }

    /**
     * Get entity repository
     *
     * @return MysqlDatabaseRepository
     */
    protected function _getRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:MysqlDatabase');
    }

    /**
     * Get database prefix
     *
     * @throws \Exception
     * @return string
     */
    protected function _getPrefix() {
        $prefix = $this->_systemconfig->getParameter('mysql.dbprefix');

        if (empty($prefix)) {
            $msg = '(MysqlBundle) Empty database prefix!';
            $this->_logger->err($msg);
            throw new \Exception($msg);
        }

        return $prefix;
    }

    /**
     * Delete obsolete MySQL databases
     */
    public function deleteObsoleteDatabases() {
        foreach ($this->_mysqladmin->getDatabases($this->_getPrefix()) as $database) {
            $mysqldb = $this
                ->_getRepository()
                ->findOneBy(array('name' => $database->getName()));

            if (!$mysqldb) {
                $this->_logger->alert('(MysqlBundle) Deleting obsolete database: ' . $database->getName());
                $this->_mysqladmin->dropDatabase($database);
            }
        }
    }

    /**
     * Delete obsolete MySQL users
     */
    public function deleteObsoleteUsers() {
        foreach ($this->_mysqladmin->getUsers($this->_getPrefix()) as $user) {
            $mysqldb = $this
                ->_getRepository()
                ->findOneBy(array('name' => $user->getUsername()));

            if (!$mysqldb) {
                $this->_logger->alert('(MysqlBundle) Deleting obsolete user: ' . $user->getUsername());
                $this->_mysqladmin->dropUser($user);
            }
        }
    }

    /**
     * Create MySQL databases, users and grant permissions
     */
    public function createDatabases() {
        /** @var $dbs MysqlDatabase[] */
        $dbs = $this
            ->_getRepository()
            ->findAll();

        foreach ($dbs as $db) {
            $userModel = new MysqlUserModel();
            $userModel
                ->setUsername($db->getName())
                ->setPassword($this->_cryptservice->decrypt($db->getPassword()));

            $dbModel = new MysqlDatabaseModel();
            $dbModel
                ->setName($db->getName())
                ->setUsers(array($userModel));

            if ($this->_mysqladmin->checkUserExists($userModel)) {
                $this->_mysqladmin->setUserPassword($userModel);
            } else {
                $this->_logger->info('(MysqlBundle) Adding user: ' . $userModel->getUsername());
                $this->_mysqladmin->createUser($userModel);
            }

            if (!$this->_mysqladmin->checkDatabaseExists($dbModel)) {
                $this->_logger->info('(MysqlBundle) Creating database: ' . $dbModel->getName());
                $this->_mysqladmin->createDatabase($dbModel);
                $this->_mysqladmin->grantPermissionsOnDatabase($dbModel);
            }
        }
    }
}
