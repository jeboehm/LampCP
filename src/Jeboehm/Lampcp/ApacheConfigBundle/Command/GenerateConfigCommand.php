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

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\ProtectionBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\PathOptionBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\BuilderChangeRepository;
use Jeboehm\Lampcp\CoreBundle\Utilities\ExecUtility;

class GenerateConfigCommand extends AbstractCommand {
	/**
	 * Get watched entitys
	 *
	 * @return array
	 */
	protected function _getEntitys() {
		$entitys = array(
			'Jeboehm\Lampcp\CoreBundle\Entity\Domain',
			'Jeboehm\Lampcp\CoreBundle\Entity\Subdomain',
			'Jeboehm\Lampcp\CoreBundle\Entity\PathOption',
			'Jeboehm\Lampcp\CoreBundle\Entity\Protection',
			'Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser',
			'Jeboehm\Lampcp\CoreBundle\Entity\IpAddress',
			'Jeboehm\Lampcp\CoreBundle\Entity\Certificate',
		);

		return $entitys;
	}

	/**
	 * @return VhostBuilderService
	 */
	protected function _getVhostBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_apache_config_vhostbuilder');
	}

	/**
	 * @return DirectoryBuilderService
	 */
	protected function _getDirectoryBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_apache_config_directorybuilder');
	}

	/**
	 * @return ProtectionBuilderService
	 */
	protected function _getProtectionBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_apache_config_protectionbuilder');
	}

	/**
	 * @return PathOptionBuilderService
	 */
	protected function _getPathOptionBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_apache_config_pathoptionbuilder');
	}

	/**
	 * @return CertificateBuilderService
	 */
	protected function _getCertificateBuilderService() {
		return $this->getContainer()->get('jeboehm_lampcp_apache_config_certificatebuilder');
	}

	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:apache:generateconfig');
		$this->setDescription('Generates the apache2 configuration');
		$this->addOption('force', 'f', InputOption::VALUE_NONE);
	}

	/**
	 * Execute command
	 *
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @throws \Exception
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$run = false;

		if($input->getOption('force') || $this->_isChanged()) {
			$run = true;
		}

		if($run) {
			$this->_getLogger()->info('(GenerateConfigCommand) Executing...');

			if($input->getOption('verbose')) {
				$output->writeln('(GenerateConfigCommand) Executing...');
			}

			try {
				$certificate = $this->_getCertificateBuilderService();
				$certificate->buildAll();

				$directory = $this->_getDirectoryBuilderService();
				$directory->buildAll();

				$vhost = $this->_getVhostBuilderService();
				$vhost->buildAll();

				$protection = $this->_getProtectionBuilderService();
				$protection->buildAll();

				$pathoption = $this->_getPathOptionBuilderService();
				$pathoption->buildAll();

				$this->_restartApache();
			} catch(\Exception $e) {
				$this->_getLogger()->err('(GenerateConfigCommand) Error: ' . $e->getMessage());

				throw $e;
			}
		} else {
			if($input->getOption('verbose')) {
				$output->writeln('(GenerateConfigCommand) No changes detected.');
			}
		}
	}

	/**
	 * Checks for changed entitys that are relevant for this task
	 *
	 * @return bool
	 */
	protected function _isChanged() {
		/** @var $repo BuilderChangeRepository */
		$repo = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:BuilderChange');
		$data = $repo->getByEntitynamesArray($this->_getEntitys());

		if(count($data) > 0) {
			foreach($data as $entity) {
				$this->_getDoctrine()->remove($entity);
			}

			$this->_getDoctrine()->flush();

			return true;
		}

		return false;
	}

	/**
	 * Restart apache2
	 */
	protected function _restartApache() {
		$exec = new ExecUtility();
		$cmd  = $this
			->_getConfigService()
			->getParameter('apache.cmdapache2restart');

		if(!empty($cmd)) {
			$this->_getLogger()->info('(GenerateConfigCommand) Restarting apache2...');

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
