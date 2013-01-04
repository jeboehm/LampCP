<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Jboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\User;

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
			->_getSystemConfigService()
			->getParameter('systemconfig.option.paths.web.root.dir') . '/lampcp';

		if(!is_dir($dir . '/htdocs/app')) {
			$msg = '(GenerateLampcpConfigCommand) Could not generate config, because this is not a supported directory structure. Expecting LampCP in ' . $dir . '/htdocs';
			$this->_getLogger()->err($msg);

			throw new \Exception($msg);
		}

		$directory = $this->_getDirectoryBuilderService();
		$vhost     = $this->_getVhostBuilderService();
		$domain    = $this->_getFakeDomain($input->getArgument('vhost'), $input->getArgument('user'), $dir);

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
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Domain
	 */
	protected function _getFakeDomain($servername, $username, $dir) {
		/** @var $user User */
		$user = $this->_getDoctrine()->getRepository('JboehmLampcpCoreBundle:User')->findOneBy(array(
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

		return $domain;
	}
}
