<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Command;

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Command\ConfigBuilderCommandInterface;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class GenerateConfigCommand
 *
 * Generate configuration for PHP-FPM.
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateConfigCommand extends AbstractCommand implements ConfigBuilderCommandInterface
{
    /**
     * Run build process.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->_isEnabled() && !$input->getOption('force')) {
            $output->writeln('Command not enabled.');

            return false;
        }

        $run = false;

        if ($this->_isChanged() || $input->getOption('force')) {
            $run = true;
        }

        if ($run) {
            $this
                ->_getConfigBuilderService()
                ->deleteOldPools();

            $this
                ->_getConfigBuilderService()
                ->createPools();

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
            ->getParameter('phpfpm.enabled');
    }

    /**
     * Test if relevant data is changed.
     *
     * @return bool
     */
    protected function _isChanged()
    {
        return $this
            ->_getCronService()
            ->checkEntitiesChanged($this->getName(), self::getListenEntities());
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
     * A list of entities that require an execution
     * of this command when they are changed.
     *
     * @return array
     */
    public static function getListenEntities()
    {
        return array(
            'JeboehmLampcpCoreBundle:Domain',
            'JeboehmLampcpCoreBundle:Subdomain',
        );
    }

    /**
     * Get config builder.
     *
     * @return ConfigBuilderService
     */
    protected function _getConfigBuilderService()
    {
        return $this
            ->getContainer()
            ->get('jeboehm_lampcp_php_fpm.configbuilderservice');
    }

    /**
     * Restart the PHP-FPM process.
     *
     * @return bool
     */
    protected function _restartProcess()
    {
        $cmd = $this
            ->_getConfigService()
            ->getParameter('phpfpm.cmd.reload');

        if (empty($cmd)) {
            return false;
        }

        $proc = new Process($cmd);
        $proc->run();

        if ($proc->getExitCode() > 0) {
            $this
                ->_getLogger()
                ->error('Could not restart PHP-FPM!');

            return false;
        } else {
            $this
                ->_getLogger()
                ->info('Restarted PHP-FPM!');
        }

        return true;
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName(self::getCommandName());
        $this->setDescription('Generates the PHP-FPM configuration');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * Get the command's name.
     *
     * @return string
     */
    public static function getCommandName()
    {
        return 'lampcp:phpfpm:generateconfig';
    }
}
