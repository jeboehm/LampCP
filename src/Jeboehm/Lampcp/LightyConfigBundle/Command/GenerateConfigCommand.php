<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Jeboehm\Lampcp\ApacheConfigBundle\Command\GenerateConfigCommand as ParentConfigCommand;
use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\FcgiStarterService;

/**
 * Class GenerateConfigCommand
 *
 * Generate configuration for Lighttpd.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends ParentConfigCommand {
    /**
     * @return VhostBuilderService
     */
    protected function _getVhostBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_vhostbuilder');
    }

    /**
     * @return DirectoryBuilderService
     */
    protected function _getDirectoryBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_directorybuilder');
    }

    /**
     * @return ProtectionBuilderService
     */
    protected function _getProtectionBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_protectionbuilder');
    }

    /**
     * @return CertificateBuilderService
     */
    protected function _getCertificateBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_certificatebuilder');
    }

    /**
     * @return FcgiStarterService
     */
    protected function _getFcgiStarterService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_fcgistarterservice');
    }

    /**
     * Configure command
     */
    protected function configure() {
        $this->setName('lampcp:lighty:generateconfig');
        $this->setDescription('Generates the lighttpd configuration');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Restart Lighttpd.
     */
    protected function _restartApache() {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('lighttpd.cmdlighttpdrestart');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart lighttpd.');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted lighttpd.');
        }

        return true;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        if (!$this->_isEnabled()) {
            $this
                ->_getLogger()
                ->err('(LightyConfigBundle) Command not enabled!');

            return;
        }

        parent::execute($input, $output);

        $this
            ->_getFcgiStarterService()
            ->buildAll();
    }

    /**
     * Get "enabled" from config service
     *
     * @return string
     */
    protected function _isEnabled() {
        return $this
            ->_getConfigService()
            ->getParameter('lighttpd.enabled');
    }
}
