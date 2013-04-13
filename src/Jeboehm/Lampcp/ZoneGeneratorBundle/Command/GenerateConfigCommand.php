<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Service\BuilderService;

/**
 * Class GenerateConfigCommand
 *
 * Generate Bind zonefiles.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends AbstractCommand {
    /**
     * Get watched entities.
     *
     * @return array
     */
    protected function _getEntitys() {
        $entitys = array(
            'Jeboehm\Lampcp\CoreBundle\Entity\Dns',
        );

        return $entitys;
    }

    /**
     * Configure command.
     */
    protected function configure() {
        $this->setName('lampcp:zone:generateconfig');
        $this->setDescription('Generates the zonefiles');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @return int|null|void
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
                ->_getLogger()
                ->info('Building dns configuration...');

            $builder = $this->_getBuilderService();
            $builder->build();

            $this->_restartBind();

            $this
                ->_getCronService()
                ->updateLastRun($this->getName());
        }
    }

    /**
     * Checks for changed entitys that are relevant for this task.
     *
     * @return bool
     */
    protected function _isChanged() {
        $last = $this
            ->_getCronService()
            ->getLastRun($this->getName());

        /**
         * First run
         */
        if (!$last) {
            return true;
        } else {
            /**
             * Find entities newer than $last
             */
            foreach ($this->_getEntitys() as $entity) {
                $result = $this
                    ->_getChangeTrackingService()
                    ->findNewer($entity, $last);

                if (count($result) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get builder service.
     *
     * @return BuilderService
     */
    protected function _getBuilderService() {
        /** @var $service BuilderService */
        $service = $this
            ->getContainer()
            ->get('jeboehm_lampcp_zonegenerator.builderservice');

        return $service;
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
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled() {
        return $this
            ->_getConfigService()
            ->getParameter('dns.enabled');
    }

    /**
     * Restart Bind.
     *
     * @return bool
     */
    protected function _restartBind() {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('dns.cmd.reload');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart bind!');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted bind!');
        }

        return true;
    }
}
