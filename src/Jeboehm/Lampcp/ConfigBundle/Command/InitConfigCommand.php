<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigProviderCollector;

/**
 * Class InitConfigCommand
 *
 * Search for all Config Providers and execute them.
 *
 * @package Jeboehm\Lampcp\ConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class InitConfigCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure() {
        $this->setName('lampcp:config:init');
        $this->setDescription('Initialize the LampCP configuration');
    }

    /**
     * Execute command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var $collector ConfigProviderCollector */
        $collector = $this
            ->getContainer()
            ->get('jeboehm_lampcp_config_configprovidercollector');
        $providers = $collector->getProviders();

        foreach ($providers as $provider) {
            $provider->init();
        }
    }
}
