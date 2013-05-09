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

use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\ProtectionBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class GenerateConfigCommand
 *
 * Generates the Apache2 configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends AbstractCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('lampcp:apache:generateconfig')
            ->setDescription('Generates the Apache2 configuration.')
            ->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->_isEnabled()) {
            $output->writeln('Command not enabled');

            return false;
        }

        $run = false;

        if ($input->getOption('force') || $this->_isChanged()) {
            $run = true;
        }

        if ($run) {
            $domains = $this->_getDomains();

            $this->_buildCertificates();
            $this->_buildDirectories($domains);
            $this->_buildVhosts($domains);
            $this->_buildProtection($domains);

            $this->_restartProcess();

            $this
                ->_getCronService()
                ->updateLastRun($this->getName());

            return true;
        }

        return false;
    }

    /**
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return $this
            ->_getConfigService()
            ->getParameter('apache.enabled');
    }

    /**
     * Checks for changed entitys that are relevant for this task.
     *
     * @return bool
     */
    protected function _isChanged()
    {
        return $this
            ->_getCronService()
            ->checkEntitiesChanged($this->getName(), $this->_getEntities());
    }

    /**
     * Get cron service.
     *
     * @return CronService
     */
    protected function _getCronService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_core.cronservice');
    }

    /**
     * Get watched entities.
     *
     * @return array
     */
    protected function _getEntities()
    {
        $entities = array(
            'JeboehmLampcpCoreBundle:Domain',
            'JeboehmLampcpCoreBundle:Subdomain',
            'JeboehmLampcpCoreBundle:PathOption',
            'JeboehmLampcpCoreBundle:Protection',
            'JeboehmLampcpCoreBundle:ProtectionUser',
            'JeboehmLampcpCoreBundle:IpAddress',
            'JeboehmLampcpCoreBundle:Certificate',
        );

        return $entities;
    }

    /**
     * Return Domains.
     *
     * @return Domain[]
     */
    protected function _getDomains()
    {
        /** @var EntityRepository $repository */
        $repository = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain');

        return $repository->findAll();
    }

    /**
     * Use CertificateBuilderService to build certificates.
     */
    protected function _buildCertificates()
    {
        /** @var EntityRepository $repository */
        $repository = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Certificate');

        $certificates = $repository->findAll();

        /*
         * Build certificates.
         */
        $this
            ->_getCertificateBuilderService()
            ->setCertificates($certificates)
            ->buildCertificates();

        $this
            ->_getDoctrine()
            ->flush();
    }

    /**
     * Get certificate builder service.
     *
     * @return CertificateBuilderService
     */
    protected function _getCertificateBuilderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_certificatebuilder');
    }

    /**
     * Use DirectoryBuilderService to build directories for domain.
     *
     * @param Domain[] $domains
     */
    protected function _buildDirectories(array $domains)
    {
        $directoryBuilder = $this->_getDirectoryBuilderService();

        foreach ($domains as $domain) {
            $directoryBuilder->createDirectories($domain);
        }
    }

    /**
     * Get directory builder service.
     *
     * @return DirectoryBuilderService
     */
    protected function _getDirectoryBuilderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_directorybuilder');
    }

    /**
     * Use VhostBuilderService to build vhosts.
     *
     * @param Domain[] $domains
     */
    protected function _buildVhosts(array $domains)
    {
        $configdir = $this
            ->_getConfigService()
            ->getParameter('apache.pathapache2conf');

        $vhostBuilder = $this->_getVhostBuilderService();
        $vhostBuilder
            ->setDomains($domains)
            ->setConfigdir($configdir)
            ->collectVhostModels();
        $vhostBuilder->setPhpSocketToVhosts();

        $vhostBuilder->buildConfiguration();
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService
     */
    protected function _getVhostBuilderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_vhostbuilder');
    }

    /**
     * Use ProtectionBuilderService to build protections.
     *
     * @param Domain[] $domains
     */
    protected function _buildProtection(array $domains)
    {
        foreach ($domains as $domain) {
            $this
                ->_getProtectionBuilderService()
                ->removeObsoleteAuthUserFiles($domain);

            foreach ($domain->getProtection() as $protection) {
                /** @var Protection $protection */
                $this
                    ->_getProtectionBuilderService()
                    ->createAuthUserFile($protection);
            }
        }
    }

    /**
     * Get protection builder service.
     *
     * @return ProtectionBuilderService
     */
    protected function _getProtectionBuilderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_protectionbuilder');
    }

    /**
     * Restart apache2.
     */
    protected function _restartProcess()
    {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('apache.cmdapache2restart');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart Apache2.');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted Apache2.');
        }

        return true;
    }
}
