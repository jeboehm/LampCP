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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\LightyConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Command\GenerateLampcpConfigCommand as BaseGenerateLampcpConfigCommand;

/**
 * Class GenerateLampcpConfigCommand
 *
 * Generate first vhost for new LampCP installations.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateLampcpConfigCommand extends BaseGenerateLampcpConfigCommand {
    /**
     * Configure command.
     */
    protected function configure() {
        parent::configure();
        $this->setName('lampcp:lighty:generatelampcpconfig');
        $this->setDescription('Generates the lighttpd configuration for LampCP');
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService.
     */
    protected function _getVhostBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_vhostbuilder');
    }

    /**
     * Get directory builder service.
     *
     * @return DirectoryBuilderService.
     */
    protected function _getDirectoryBuilderService() {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_directorybuilder');
    }

    /**
     * {@inheritdoc}
     */
    protected function _getLampcpDomain($servername, $username, $dir) {
        $domain       = parent::_getLampcpDomain($servername, $username, $dir);
        $customconfig = <<< EOT
url.rewrite-if-not-file = (
    "(.+)" => "/app.php$1"
)

EOT;

        $domain->setCustomconfig($customconfig);
        $this
            ->_getDoctrine()
            ->persist($domain);
        $this
            ->_getDoctrine()
            ->flush();

        return $domain;
    }
}
