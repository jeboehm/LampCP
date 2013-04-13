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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;

/**
 * Class GenerateLampcpConfigCommand
 *
 * Generates the first vhost for new LampCP installations.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateLampcpConfigCommand extends GenerateConfigCommand {
    /**
     * Configure command.
     */
    protected function configure() {
        $this->setName('lampcp:apache:generatelampcpconfig');
        $this->setDescription('Generates the apache2 configuration for LampCP');
        $this->addArgument('vhost', InputArgument::REQUIRED, 'LampCP ServerName. Example: lampcp.example.org');
        $this->addArgument('user', InputArgument::REQUIRED, 'LampCP will run as this user.');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $dir = $this
            ->_getConfigService()
            ->getParameter('apache.pathwww') . '/lampcp';

        if (!is_dir($dir . '/htdocs/app')) {
            $output->writeln('Could not generate config, because this is not a supported directory structure. Expecting LampCP in ' . $dir . '/htdocs');

            return false;
        }

        $directory = $this->_getDirectoryBuilderService();
        $vhost     = $this->_getVhostBuilderService();

        // Verzeichnisse erzeugen
        $directory->buildAll();

        // Configs erzeugen
        $vhost->buildAll();
    }

    /**
     * Generate fake domain.
     *
     * @param string $servername
     * @param string $username
     * @param string $dir
     *
     * @throws \Exception
     * @return Domain
     */
    protected function _getLampcpDomain($servername, $username, $dir) {
        $domain = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain')
            ->findOneBy(array('domain' => $servername));

        if ($domain) {
            return $domain;
        }

        /** @var $user User */
        $user = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:User')
            ->findOneBy(array(
                             'name' => $username,
                        ));

        if (!$user) {
            $msg = 'Cannot find User ' . $username . '!';

            throw new \Exception($msg);
        }

        $domain = new Domain();
        $domain
            ->setPath($dir)
            ->setWebroot('htdocs/web')
            ->setDomain($servername)
            ->setUser($user);

        $this
            ->_getDoctrine()
            ->persist($domain);
        $this
            ->_getDoctrine()
            ->flush();

        return $domain;
    }
}
