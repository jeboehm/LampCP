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
use Jeboehm\Lampcp\CoreBundle\Command\ConfigBuilderCommandInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabaseRepository;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\MysqlBundle\Service\SyncService;
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
class GenerateDatabasesCommand extends AbstractCommand implements ConfigBuilderCommandInterface
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName(self::getCommandName());
        $this->setDescription('Generates (and deletes) MySQL Databases');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Get the command's name.
     *
     * @return string
     */
    public static function getCommandName()
    {
        return 'lampcp:mysql:generatedatabases';
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->_isEnabled() && !$input->getOption('force')) {
            $output->writeln('Command not enabled.');

            return false;
        }

        $run = false;

        if ($input->getOption('force') || $this->_isChanged()) {
            $run = true;
        }

        if ($run) {
            $sync = $this->getSyncService();

            foreach ($this
                         ->getRepository()
                         ->findAll() as $entity) {
                $sync->addEntity($entity);
            }

            $sync->findAndDeleteOldUsers();
            $sync->findAndDeleteOldDatabases();
            $sync->createAndUpdateUsersAndDatabases();

            $this
                ->_getCronService()
                ->updateLastRun($this->getName());

            return true;
        }

        return false;
    }

    /**
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return $this
            ->_getConfigService()
            ->getParameter('mysql.enabled');
    }

    /**
     * Checks for changed entitys that are relevant for this task.
     *
     * @return bool
     */
    protected function _isChanged()
    {
        return $this
            ->_getCronService()
            ->checkEntitiesChanged($this->getName(), self::getListenEntities());
    }

    /**
     * Get cron service.
     *
     * @return CronService
     */
    protected function _getCronService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_core.cronservice');
    }

    /**
     * A list of entities that require an execution
     * of this command when they are changed.
     *
     * @return array
     */
    public static function getListenEntities()
    {
        return array(
            'JeboehmLampcpCoreBundle:MysqlDatabase',
        );
    }

    /**
     * Get sync service.
     *
     * @return SyncService
     */
    protected function getSyncService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.service.syncservice');
    }

    /**
     * Get repository.
     *
     * @return MysqlDatabaseRepository
     */
    protected function getRepository()
    {
        return $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:MysqlDatabase');
    }

    /**
     * Get change tracking service.
     *
     * @return ChangeTrackingService
     */
    protected function _getChangeTrackingService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_core.changetrackingservice');
    }
}
