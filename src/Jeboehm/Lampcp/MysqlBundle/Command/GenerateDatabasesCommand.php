<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Command;

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\MysqlBundle\Service\MysqlAdminService;
use Jeboehm\Lampcp\MysqlBundle\Service\MysqlSynchronizerService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateDatabasesCommand
 *
 * Create, delete and update MySQL databases.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateDatabasesCommand extends AbstractCommand {
    /** @var MysqlAdminService */
    protected $_mysqladminservice;

    /** @var MysqlSynchronizerService */
    protected $_mysqlsyncservice;

    /**
     * Get watched entities.
     *
     * @return array
     */
    protected function _getEntities() {
        $entitys = array(
            'JeboehmLampcpCoreBundle:MysqlDatabase',
        );

        return $entitys;
    }

    /**
     * Get mysql admin service.
     *
     * @return MysqlAdminService
     */
    protected function _getMysqlAdminService() {
        if (!$this->_mysqladminservice) {
            $this->_mysqladminservice = $this
                ->getContainer()
                ->get('jeboehm_lampcp_mysql.mysqladminservice');
            $this->_mysqlAdminServiceConnect();
        }

        return $this->_mysqladminservice;
    }

    /**
     * Initialize mysql admin service.
     */
    protected function _mysqlAdminServiceConnect() {
        $this
            ->_getMysqlAdminService()
            ->connect($this
                ->_getConfigService()
                ->getParameter('mysql.host'), $this
                ->_getConfigService()
                ->getParameter('mysql.rootuser'), $this
                ->_getConfigService()
                ->getParameter('mysql.rootpassword'), $this
                ->_getConfigService()
                ->getParameter('mysql.port'));
    }

    /**
     * Get mysql synchronizer service.
     *
     * @return MysqlSynchronizerService
     */
    protected function _getMysqlSynchronizerService() {
        if (!$this->_mysqlsyncservice) {
            $this->_getMysqlAdminService();
            $this->_mysqlsyncservice = $this
                ->getContainer()
                ->get('jeboehm_lampcp_mysql.mysqlsynchronizerservice');
        }

        return $this->_mysqlsyncservice;
    }

    /**
     * Configure command.
     */
    protected function configure() {
        $this->setName('lampcp:mysql:generatedatabases');
        $this->setDescription('Generates (and deletes) MySQL Databases');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$this->_isEnabled()) {
            $output->writeln('Command not enabled.');

            return false;
        }

        $run = false;

        if ($input->getOption('force') || $this->_isChanged()) {
            $run = true;
        }

        if ($run) {
            $this
                ->_getMysqlSynchronizerService()
                ->createDatabases();

            $this
                ->_getMysqlSynchronizerService()
                ->deleteObsoleteDatabases();

            $this
                ->_getMysqlSynchronizerService()
                ->deleteObsoleteUsers();

            $this
                ->_getCronService()
                ->updateLastRun($this->getName());

            return true;
        }

        return false;
    }

    /**
     * Checks for changed entitys that are relevant for this task.
     *
     * @return bool
     */
    protected function _isChanged() {
        return $this
            ->_getCronService()
            ->checkEntitiesChanged($this->getName(), $this->_getEntities());
    }

    /**
     * Get cron service.
     *
     * @return CronService
     */
    protected function _getCronService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_core.cronservice');
    }

    /**
     * Get change tracking service.
     *
     * @return ChangeTrackingService
     */
    protected function _getChangeTrackingService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_core.changetrackingservice');
    }

    /**
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled() {
        return $this
            ->_getConfigService()
            ->getParameter('mysql.enabled');
    }
}
