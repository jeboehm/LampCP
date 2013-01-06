<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\MysqlBundle\Command;

use Jboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jboehm\Lampcp\MysqlBundle\Service\MysqlAdminService;

class GenerateDatabasesCommand extends AbstractCommand {
	/** @var MysqlAdminService */
	protected $_mysqladminservice;

	/**
	 * Get watched entitys
	 *
	 * @return array
	 */
	protected function _getEntitys() {
		$entitys = array(
			'Jboehm\Lampcp\CoreBundle\Entity\MysqlDatabase',
		);

		return $entitys;
	}

	/**
	 * @return \Jboehm\Lampcp\MysqlBundle\Service\MysqlAdminService
	 */
	protected function _getMysqlAdminService() {
		if(!$this->_mysqladminservice) {
			$this->_mysqladminservice = $this->getContainer()->get('jboehm_lampcp_mysql.mysqladminservice');
		}

		return $this->_mysqladminservice;
	}

	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:mysql:generatedatabases');
		$this->setDescription('Generates (and deletes) MySQL Databases');
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

		if($input->getOption('force') || $this->_isChanged()) {
			$run = true;
		}

		if($run) {
			$this->_getLogger()->info('(GenerateDatabasesCommand) Executing...');

			if($input->getOption('verbose')) {
				$output->writeln('(GenerateDatabasesCommand) Executing...');
			}

			$this->_getMysqlAdminService()->createAndDeleteDatabases();
		} else {
			if($input->getOption('verbose')) {
				$output->writeln('(GenerateDatabasesCommand) No changes detected.');
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
		$repo = $this->_getDoctrine()->getRepository('JboehmLampcpCoreBundle:BuilderChange');
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
}
