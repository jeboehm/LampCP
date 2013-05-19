<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Command;

use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\SetupBundle\Model\Exception\UserNotFoundException;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Vhost;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\VhostValidator;
use Jeboehm\Lampcp\UserLoaderBundle\Service\UserLoaderService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class VhostCommand
 *
 * @package Jeboehm\Lampcp\SetupBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostCommand extends AbstractCommand
{
    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->setupLampcp();

        $vhost  = $this->_configureVhost();
        $domain = $this->_generateLampcpDomain($vhost);

        $this->persistDomain($domain);

        $this
            ->getOutput()
            ->writeln(
                array(
                     '<info>The vhost is configured successfully.</info>',
                     'See https://github.com/jeboehm/LampCP/wiki/Installation for further instructions.',
                )
            );
    }

    /**
     * Run different LampCP Setup tasks.
     */
    protected function setupLampcp()
    {
        $this->runConsoleCommand(
            array(
                 'command' => 'doctrine:schema:update',
                 '--force' => true,
            )
        );

        $this->runConsoleCommand(
            array(
                 'command' => 'lampcp:config:init',
            )
        );

        $this->runConsoleCommand(
            array(
                 'command' => 'lampcp:postfix:generateview',
                 '-c'      => true,
            )
        );

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

    /**
     * Vhost setup.
     *
     * @return Vhost
     */
    protected function _configureVhost()
    {
        $this
            ->getOutput()
            ->writeln('<info>Configure the LampCP vhost:</info>');

        /** @var VhostValidator $validator */
        $validator = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.validator.vhost');
        $vhost     = new Vhost();

        $this
            ->getFormGenerator()
            ->processForm($vhost);

        if (!$this->checkResult($validator->validate($vhost))) {
            $this->_configureVhost();
        }

        return $vhost;
    }

    /**
     * Generate LampCP Domain.
     *
     * @param Vhost $vhost
     *
     * @return Domain
     * @throws UserNotFoundException
     */
    protected function _generateLampcpDomain(Vhost $vhost)
    {
        $domain = new Domain();
        $ip     = new IpAddress();

        /** @var User $user */
        $user = $this
            ->getUserRepository()
            ->findOneBy(array('name' => $vhost->user));

        if (!$user) {
            throw new UserNotFoundException();
        }

        $ip
            ->setAlias('Default ip address')
            ->setIp($vhost->ipaddress);

        $domain
            ->setDomain($vhost->address)
            ->setPath(realpath(__DIR__ . '/../../../../../../'))
            ->setWebroot('htdocs/web')
            ->setUser($user)
            ->getIpaddress()
            ->add($ip);

        return $domain;
    }

    /**
     * Get user repository.
     *
     * @return EntityRepository
     */
    protected function getUserRepository()
    {
        return $this
            ->getUserLoaderService()
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:User');
    }

    /**
     * Persist domain.
     *
     * @param Domain $domain
     */
    protected function persistDomain(Domain $domain)
    {
        $this
            ->getUserLoaderService()
            ->getEm()
            ->persist($domain);

        $this
            ->getUserLoaderService()
            ->getEm()
            ->flush();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('lampcp:setup:vhost')
            ->setDescription('Configure the LampCP vhost.');
    }
}
