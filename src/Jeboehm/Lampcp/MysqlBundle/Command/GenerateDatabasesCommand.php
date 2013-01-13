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
use Jeboehm\Lampcp\MysqlBundle\Model\MysqlUserModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jeboehm\Lampcp\MysqlBundle\Service\MysqlAdminService;
use Jeboehm\Lampcp\MysqlBundle\Service\MysqlSynchronizerService;

class GenerateDatabasesCommand extends AbstractCommand {
	/** @var MysqlAdminService */
	protected $_mysqladminservice;

	/** @var MysqlSynchronizerService */
	protected $_mysqlsyncservice;

	/**
	 * Get watched entitys
	 *
	 * @return array
	 */
	protected function _getEntitys() {
		$entitys = array(
			'Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase',
		);

		return $entitys;
	}

	/**
	 * @return \Jeboehm\Lampcp\MysqlBundle\Service\MysqlAdminService
	 */
	protected function _getMysqlAdminService() {
		if(!$this->_mysqladminservice) {
			$this->_mysqladminservice = $this->getContainer()->get('jeboehm_lampcp_mysql.mysqladminservice');
			$this->_mysqlAdminServiceConnect();
		}

		return $this->_mysqladminservice;
	}

	/**
	 * Initialize MySQLAdminService
	 */
	protected function _mysqlAdminServiceConnect() {
		$this->_getMysqlAdminService()->connect(
			$this->getContainer()->getParameter('database_host'),
			$this->_getConfigService()->getParameter('mysql.rootuser'),
			$this->_getConfigService()->getParameter('mysql.rootpassword'),
			$this->getContainer()->getParameter('database_port')
		);
	}

	/**
	 * Get MysqlSynchronizerService
	 *
	 * @return \Jeboehm\Lampcp\MysqlBundle\Service\MysqlSynchronizerService
	 */
	protected function _getMysqlSynchronizerService() {
		if(!$this->_mysqlsyncservice) {
			$this->_getMysqlAdminService();
			$this->_mysqlsyncservice = $this
				->getContainer()
				->get('jeboehm_lampcp_mysql.mysqlsynchronizerservice');
		}

		return $this->_mysqlsyncservice;
	}

	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:mysql:generatedatabases');
		$this->setDescription('Generates (and deletes) MySQL Databases');
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
			$this->_getLogger()->info('(GenerateDatabasesCommand) Executing...');

			if($input->getOption('verbose')) {
				$output->writeln('(GenerateDatabasesCommand) Executing...');
			}

			$this->_getMysqlSynchronizerService()->createDatabases();
			$this->_getMysqlSynchronizerService()->deleteObsoleteDatabases();
			$this->_getMysqlSynchronizerService()->deleteObsoleteUsers();

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
}
