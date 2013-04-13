<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UserLoaderBundle\Command;

use Jboehm\Bundle\PasswdBundle\Model\PasswdService;
use Jboehm\Bundle\PasswdBundle\Model\GroupService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jboehm\Bundle\PasswdBundle\Model\Group;

/**
 * Class LoadUsersCommand
 *
 * Collects system users and creates a cache in the database.
 *
 * @package Jeboehm\Lampcp\UserLoaderBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class LoadUsersCommand extends AbstractCommand {
    /** @var EntityRepository */
    protected $_localUserRepository;

    /** @var PasswdService */
    protected $_systemUserService;

    /** @var GroupService */
    protected $_systemGroupService;

    /** @var EntityRepository */
    protected $_domainRepository;

    /**
     * Setting up the Command.
     *
     * @return void
     */
    protected function configure() {
        $this->setName('lampcp:loadusers');
        $this->setDescription('Loads the users from system into LampCP.');
    }

    /**
     * Runs the Command.
     *
     * @param InputInterface  $input  The Input Interface, contains arguments and options
     * @param OutputInterface $output The Output Interface, to display formated output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->_localUserRepository = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:User');
        $this->_domainRepository    = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain');
        $this->_systemUserService   = new PasswdService($this
            ->_getConfigService()
            ->getParameter('unix.passwdfile'));
        $this->_systemGroupService  = new GroupService($this
            ->_getConfigService()
            ->getParameter('unix.groupfile'));

        if ($input->getOption('verbose')) {
            $output->writeln('Found ' . count($this->_systemUserService->getAll()) . ' system users...');
            $output->writeln('Found ' . count($this->_localUserRepository->findAll()) . ' cached users...');
        }

        $this
            ->_getLogger()
            ->info('(UserLoaderBundle) Executing...');

        $this->_syncSystemToLocal($output, $input);
        $this->_checkDeleted($output, $input);

        $this
            ->_getDoctrine()
            ->flush();
    }

    /**
     * Syncs system users to local cache.
     *
     * @param OutputInterface $output
     * @param InputInterface  $input
     */
    protected function _syncSystemToLocal(OutputInterface $output, InputInterface $input) {
        foreach ($this->_systemUserService->getAll() as $systemUser) {
            /** @var $localUser User */
            $localUser = $this->_localUserRepository->findOneBy(array('name' => $systemUser->getName()));
            $changed   = false;

            if (!$localUser) {
                $localUser = new User();
                $localUser
                    ->setName($systemUser->getName())
                    ->setUid($systemUser->getUid())
                    ->setGid($systemUser->getGid());

                /** @var $group Group */
                $group = $this->_systemGroupService->findOneBy(array('gid' => $systemUser->getGid()));
                $localUser->setGroupname($group->getName());

                $this
                    ->_getDoctrine()
                    ->persist($localUser);

                if ($input->getOption('verbose')) {
                    $output->writeln('Saved new user: ' . $localUser->getName() . '...');
                }
            } else {
                if ($localUser->getUid() != $systemUser->getUid()) {
                    $localUser->setUid($systemUser->getUid());
                    $changed = true;
                }

                if ($localUser->getGid() != $systemUser->getGid()) {
                    $localUser->setGid($systemUser->getGid());

                    /** @var $group Group */
                    $group = $this->_systemGroupService->findOneBy(array('gid' => $systemUser->getGid()));
                    $localUser->setGroupname($group->getName());

                    $changed = true;
                }

                if ($changed) {
                    $this
                        ->_getDoctrine()
                        ->persist($localUser);

                    if ($input->getOption('verbose')) {
                        $output->writeln('Changed user: ' . $localUser->getName() . '...');
                    }
                }
            }
        }
    }

    /**
     * Checks for invalid local users.
     *
     * @param OutputInterface $output
     * @param InputInterface  $input
     */
    protected function _checkDeleted(OutputInterface $output, InputInterface $input) {
        foreach ($this->_localUserRepository->findAll() as $localUser) {
            /** @var $localUser User */

            if (!$this->_systemUserService->findOneBy(array('name' => $localUser->getName()))) {
                /** @var $domainsForUser Domain[] */
                $domainsForUser = $this->_domainRepository->findBy(array('user' => $localUser));

                if (count($domainsForUser) > 0) {
                    $logMsg = 'Could not delete invalid local user ' . $localUser->getName() . ', because ' . count($domainsForUser) . ' domains are linked to it.';
                    $this
                        ->_getLogger()
                        ->error($logMsg);
                    $output->writeln($logMsg);
                } else {
                    if ($input->getOption('verbose')) {
                        $output->writeln('Deleted user: ' . $localUser->getName() . '...');
                    }

                    $this
                        ->_getDoctrine()
                        ->remove($localUser);
                }
            }
        }
    }
}
