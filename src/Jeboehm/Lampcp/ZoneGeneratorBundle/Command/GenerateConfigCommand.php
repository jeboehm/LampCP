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

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Service\BuilderService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class GenerateConfigCommand
 *
 * Generate Bind zonefiles.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends AbstractCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
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
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return $this
            ->_getConfigService()
            ->getParameter('dns.enabled');
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
            ->checkEntitiesChanged($this->getName(), $this->_getEntities());
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
     * Get watched entities.
     *
     * @return array
     */
    protected function _getEntities()
    {
        $entitys = array(
            'Jeboehm\Lampcp\CoreBundle\Entity\Dns',
        );

        return $entitys;
    }

    /**
     * Get builder service.
     *
     * @return BuilderService
     */
    protected function _getBuilderService()
    {
        /** @var $service BuilderService */
        $service = $this
            ->getContainer()
            ->get('jeboehm_lampcp_zonegenerator.builderservice');

        return $service;
    }

    /**
     * Restart Bind.
     *
     * @return bool
     */
    protected function _restartBind()
    {
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
