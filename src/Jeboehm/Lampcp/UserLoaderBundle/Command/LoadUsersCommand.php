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

use Jeboehm\Lampcp\UserLoaderBundle\Service\UserLoaderService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoadUsersCommand
 *
 * @package Jeboehm\Lampcp\UserLoaderBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class LoadUsersCommand extends ContainerAwareCommand
{
    /**
     * Configure command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('lampcp:loadusers')
            ->setDescription('Loads system users into LampCP.');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getUserLoaderService()
            ->copyToLocal();

        $this
            ->getUserLoaderService()
            ->removeObsoleteLocalUsers();
    }

    /**
     * Get user loader service.
     *
     * @return UserLoaderService
     */
    protected function getUserLoaderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_userloader.userloaderservice');
    }
}
