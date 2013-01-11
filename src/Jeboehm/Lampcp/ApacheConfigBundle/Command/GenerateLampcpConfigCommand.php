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

class GenerateLampcpConfigCommand extends GenerateConfigCommand {
	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName('lampcp:apache:generatelampcpconfig');
		$this->setDescription('Generates the apache2 configuration for LampCP');
		$this->addArgument('vhost', InputArgument::REQUIRED, 'LampCP ServerName. Example: lampcp.example.org');
		$this->addArgument('user', InputArgument::REQUIRED, 'LampCP will run as this user.');
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
		$this->_getLogger()->info('(GenerateLampcpConfigCommand) Executing...');

		$dir = $this
			->_getConfigService()
			->getParameter('apache.pathwww') . '/lampcp';

		if(!is_dir($dir . '/htdocs/app')) {
			$msg = '(GenerateLampcpConfigCommand) Could not generate config, because this is not a supported directory structure. Expecting LampCP in ' . $dir . '/htdocs';
			$this->_getLogger()->err($msg);

			throw new \Exception($msg);
		}

		$directory = $this->_getDirectoryBuilderService();
		$vhost     = $this->_getVhostBuilderService();
		$domain    = $this->_getLampcpDomain($input->getArgument('vhost'), $input->getArgument('user'), $dir);

		// Verzeichnisse erzeugen
		$directory->createDirectorysForDomain($domain);

		// Configs erzeugen
		$vhost->buildDomain($domain);
	}

	/**
	 * Generate fake domain
	 *
	 * @param string $servername
	 * @param string $username
	 * @param string $dir
	 *
	 * @throws \Exception
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain
	 */
	protected function _getLampcpDomain($servername, $username, $dir) {
		$domain = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:Domain')
			->findOneBy(array('domain' => $servername));

		if($domain) {
			return $domain;
		}

		/** @var $user User */
		$user = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:User')
			->findOneBy(array(
							 'name' => $username,
						));

		if(!$user) {
			$msg = '(GenerateLampcpConfigCommand) Cannot find User ' . $username . '!';
			$this->_getLogger()->err($msg);

			throw new \Exception($msg);
		}

		$domain = new Domain();
		$domain
			->setPath($dir)
			->setWebroot('htdocs/web')
			->setDomain($servername)
			->setUser($user);

		$this->_getDoctrine()->persist($domain);
		$this->_getDoctrine()->flush();

		return $domain;
	}
}
