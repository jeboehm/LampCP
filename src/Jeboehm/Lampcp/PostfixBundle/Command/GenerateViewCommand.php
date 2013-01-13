<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PostfixBundle\Command;

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GenerateViewCommand extends AbstractCommand {
	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:postfix:generateview');
		$this->setDescription('Generate or drop the postfix table views');
		$this->addOption('create', 'c', InputOption::VALUE_NONE);
		$this->addOption('drop', 'd', InputOption::VALUE_NONE);
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
		if($input->getOption('create')) {
			$sql = array(
				'CREATE VIEW postfix_virtual_aliases AS select concat(MailAddress.address,"@",Domain.domain) AS source,MailAccount.username AS destination from MailAddress,MailAccount,Domain where MailAccount.id = MailAddress.mailaccount_id and Domain.id = MailAccount.domain_id',
				'CREATE VIEW postfix_virtual_domains AS select Domain.domain AS domain from Domain,MailAccount where Domain.id = MailAccount.domain_id group by Domain.id',
				'CREATE VIEW postfix_virtual_users AS select MailAccount.username AS username, MailAccount.password AS password from MailAccount where MailAccount.enabled = 1',
			);

			foreach($sql as $statement) {
				$this->_getDoctrine()->getConnection()->exec($statement);
			}

			$output->writeln('Created Postfix views!');
			$this->_getLogger()->alert('(GenerateViewsCommand) Created Postfix views');

		} elseif($input->getOption('drop')) {
			$sql = array(
				'DROP VIEW postfix_virtual_aliases',
				'DROP VIEW postfix_virtual_domains',
				'DROP VIEW postfix_virtual_users',
			);

			foreach($sql as $statement) {
				$this->_getDoctrine()->getConnection()->exec($statement);
			}

			$output->writeln('Dropped Postfix views!');
			$this->_getLogger()->alert('(GenerateViewCommand) Dropped Postfix views');
		} else {
			$output->writeln('Choose: --create or --drop');
		}
	}
}
