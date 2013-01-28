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
use Jeboehm\Lampcp\ApacheConfigBundle\Command\GenerateConfigCommand as ParentConfigCommand;
use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\CoreBundle\Utilities\ExecUtility;

class GenerateConfigCommand extends ParentConfigCommand {
	/**
	 * @return VhostBuilderService
	 */
	protected function _getVhostBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_lighty_config_vhostbuilder');
	}

	/**
	 * @return DirectoryBuilderService
	 */
	protected function _getDirectoryBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_lighty_config_directorybuilder');
	}

	/**
	 * @return ProtectionBuilderService
	 */
	protected function _getProtectionBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_lighty_config_protectionbuilder');
	}

	/**
	 * @return CertificateBuilderService
	 */
	protected function _getCertificateBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_lighty_config_certificatebuilder');
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
	 * Restart apache2
	 */
	protected function _restartApache() {
		$exec = new ExecUtility();
		$cmd  = $this
			->_getConfigService()
			->getParameter('lighttpd.cmdlighttpdrestart');

		if(!empty($cmd)) {
			$this->_getLogger()->info('(GenerateConfigCommand) Restarting Lighttpd...');

			if(strpos($cmd, ' ') !== false) {
				$cmdSplit = explode(' ', $cmd);
				$exec->exec(array_shift($cmdSplit), $cmdSplit);
			} else {
				$exec->exec($cmd);
			}

			if($exec->getCode() > 0) {
				$this->_getLogger()->err($exec->getOutput());
			}
		}
	}
}
