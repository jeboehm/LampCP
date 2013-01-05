<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Command;

use Jboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;

class GenerateConfigCommand extends AbstractCommand {
	/** @var VhostBuilderService */
	protected $_vhostBuilderService;

	/** @var DirectoryBuilderService */
	protected $_directoryBuilderService;

	/**
	 * Get watched entitys
	 *
	 * @return array
	 */
	protected function _getEntitys() {
		$entitys = array(
			'Jboehm\Lampcp\CoreBundle\Entity\Domain',
			'Jboehm\Lampcp\CoreBundle\Entity\Subdomain',
			'Jboehm\Lampcp\CoreBundle\Entity\PathOption',
			'Jboehm\Lampcp\CoreBundle\Entity\Protection',
			'Jboehm\Lampcp\CoreBundle\Entity\IpAddress',
		);

		return $entitys;
	}

	/**
	 * @return \Jboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService
	 */
	protected function _getVhostBuilderService() {
		if(!$this->_vhostBuilderService) {
			$this->_vhostBuilderService = $this->getContainer()->get('jboehm_lampcp_apache_config_vhostbuilder');
		}

		return $this->_vhostBuilderService;
	}

	/**
	 * @return \Jboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService
	 */
	protected function _getDirectoryBuilderService() {
		if(!$this->_directoryBuilderService) {
			$this->_directoryBuilderService = $this->getContainer()->get('jboehm_lampcp_apache_config_directorybuilder');
		}

		return $this->_directoryBuilderService;
	}

	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:apache:generateconfig');
		$this->setDescription('Generates the apache2 configuration');
		$this->addOption('force', InputOption::VALUE_OPTIONAL);
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
		$this->_getLogger()->info('(GenerateConfigCommand) Executing...');

		

		if($input->getOption('force')) {
			$run = true;
		}

		if($run) {
			try {
				$directory = $this->_getDirectoryBuilderService();
				$directory->createDirectorysForAllDomains();

				$vhost = $this->_getVhostBuilderService();
				$vhost->buildAll();

				$vhost->cleanVhostDirectory();
			} catch(\Exception $e) {
				$this->_getLogger()->err('(GenerateConfigCommand) Error: ' . $e->getMessage());

				throw $e;
			}
		}
	}
}
