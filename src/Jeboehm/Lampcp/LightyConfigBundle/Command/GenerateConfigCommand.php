<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Command;

use Jeboehm\Lampcp\ApacheConfigBundle\Command\GenerateConfigCommand as ParentGenerateConfigCommand;
use Jeboehm\Lampcp\CoreBundle\Command\ConfigBuilderCommandInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\LightyConfigBundle\Service\CertificateBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * Class GenerateConfigCommand
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends ParentGenerateConfigCommand implements ConfigBuilderCommandInterface
{
    /**
     * Get "enabled" from config service.
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return $this
            ->_getConfigService()
            ->getParameter('lighttpd.enabled');
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName(self::getCommandName())
            ->setDescription('Generates the Lighttpd configuration.')
            ->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Get the command's name.
     *
     * @return string
     */
    public static function getCommandName()
    {
        return 'lampcp:lighty:generateconfig';
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
            ->get('jeboehm_lampcp_lighty_config_certificatebuilder');
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
            ->get('jeboehm_lampcp_lighty_config_directorybuilder');
    }

    /**
     * Restart Lighttpd.
     *
     * @return bool
     */
    protected function _restartProcess()
    {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('lighttpd.cmdlighttpdrestart');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart Lighttpd.');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted Lighttpd.');
        }

        return true;
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
            ->getParameter('lighttpd.pathlighttpdconf');

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
            ->get('jeboehm_lampcp_lighty_config_vhostbuilder');
    }
}
