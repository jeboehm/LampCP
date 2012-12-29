<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Jboehm\Bundle\PasswdBundle\Model\PasswdService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jboehm\Lampcp\UserBundle\Entity\User;
use Jboehm\Lampcp\CoreBundle\Service\SystemConfigService;

class LoadUsersCommand extends ContainerAwareCommand {
	/** @var \Doctrine\Common\Persistence\ObjectManager */
	protected $_manager;

	/** @var \Doctrine\ORM\EntityRepository */
	protected $_localUserRepository;

	/** @var SystemConfigService */
	protected $_systemConfigService;

	/** @var PasswdService */
	protected $_systemUserService;

	/**
	 * Setting up the Command
	 *
	 * @return void
	 */
	protected function configure() {
		$this->setName('lampcp:loadusers');
		$this->setDescription('Loads the users from system into LampCP.');
	}

	/**
	 * Runs the Command
	 *
	 * @param InputInterface  $input  The Input Interface, contains arguments and options
	 * @param OutputInterface $output The Output Interface, to display formated output
	 *
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_manager             = $this->getContainer()->get('doctrine.orm.entity_manager');
		$this->_localUserRepository = $this->_manager->getRepository('JboehmLampcpUserBundle:User');
		$this->_systemConfigService = $this->getContainer()->get('jboehm_lampcp_core.systemconfigservice');
		$this->_systemUserService   = new PasswdService($this->_systemConfigService->getParameter('systemconfig.option.paths.unix.passwd.file'));

		if($input->getOption('verbose')) {
			$output->writeln('Found ' . count($this->_systemUserService->getAll()) . ' system users...');
			$output->writeln('Found ' . count($this->_localUserRepository->findAll()) . ' cached users...');
		}

		$this->_syncSystemToLocal($output, $input);
		$this->_checkDeleted($output, $input);

		$this->_manager->flush();
	}

	/**
	 * Syncs system users to local cache
	 *
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 */
	protected function _syncSystemToLocal(OutputInterface $output, InputInterface $input) {
		foreach($this->_systemUserService->getAll() as $systemUser) {
			/** @var $localUser User */
			$localUser = $this->_localUserRepository->findOneBy(array('name' => $systemUser->getName()));
			$changed   = false;

			if(!$localUser) {
				$localUser = new User();
				$localUser->setName($systemUser->getName());
				$localUser->setUid($systemUser->getUid());
				$localUser->setGid($systemUser->getGid());

				$this->_manager->persist($localUser);

				if($input->getOption('verbose')) {
					$output->writeln('Saved new user: ' . $localUser->getName() . '...');
				}
			} else {
				if($localUser->getUid() != $systemUser->getUid()) {
					$localUser->setUid($systemUser->getUid());
					$changed = true;
				}

				if($localUser->getGid() != $systemUser->getGid()) {
					$localUser->setGid($systemUser->getGid());
					$changed = true;
				}

				if($changed) {
					$this->_manager->persist($localUser);

					if($input->getOption('verbose')) {
						$output->writeln('Changed user: ' . $localUser->getName() . '...');
					}
				}
			}
		}
	}

	/**
	 * Checks for invalid local users
	 *
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 */
	protected function _checkDeleted(OutputInterface $output, InputInterface $input) {
		foreach($this->_localUserRepository->findAll() as $localUser) {
			/** @var $localUser User */
			if(!$this->_systemUserService->findOneBy(array('name' => $localUser->getName()))) {
				if($input->getOption('verbose')) {
					$output->writeln('Deleted user: ' . $localUser->getName() . '...');
				}

				$this->_manager->remove($localUser);
			}
		}
	}
}
