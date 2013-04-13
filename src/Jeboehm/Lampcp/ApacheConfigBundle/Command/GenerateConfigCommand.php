<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\ProtectionBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService;

/**
 * Class GenerateConfigCommand
 *
 * Generates the Apache2 configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Command
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
            'JeboehmLampcpCoreBundle:Domain',
            'JeboehmLampcpCoreBundle:Subdomain',
            'JeboehmLampcpCoreBundle:PathOption',
            'JeboehmLampcpCoreBundle:Protection',
            'JeboehmLampcpCoreBundle:ProtectionUser',
            'JeboehmLampcpCoreBundle:IpAddress',
            'JeboehmLampcpCoreBundle:Certificate',
        );

        return $entitys;
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService
     */
    protected function _getVhostBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_vhostbuilder');
    }

    /**
     * Get directory builder service.
     *
     * @return DirectoryBuilderService
     */
    protected function _getDirectoryBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_directorybuilder');
    }

    /**
     * Get protection builder service.
     *
     * @return ProtectionBuilderService
     */
    protected function _getProtectionBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_protectionbuilder');
    }

    /**
     * Get certificate builder service.
     *
     * @return CertificateBuilderService
     */
    protected function _getCertificateBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_certificatebuilder');
    }

    /**
     * Configure command.
     */
    protected function configure() {
        $this->setName('lampcp:apache:generateconfig');
        $this->setDescription('Generates the apache2 configuration');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$this->_isEnabled()) {
            $output->writeln('Command not enabled');

            return false;
        }

        $run = false;

        if ($input->getOption('force') || $this->_isChanged()) {
            $run = true;
        }

        if ($run) {
            $certificate = $this->_getCertificateBuilderService();
            $certificate->buildAll();

            $directory = $this->_getDirectoryBuilderService();
            $directory->buildAll();

            $vhost = $this->_getVhostBuilderService();
            $vhost->buildAll();

            $protection = $this->_getProtectionBuilderService();
            $protection->buildAll();

            $this->_restartApache();

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
     * Restart apache2.
     */
    protected function _restartApache() {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('apache.cmdapache2restart');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart Apache2.');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted Apache2.');
        }

        return true;
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
            ->getParameter('apache.enabled');
    }
}
