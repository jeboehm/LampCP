<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PostfixBundle\Command;

use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class GenerateViewCommand
 *
 * Provides commands for creating database tables.
 *
 * @package Jeboehm\Lampcp\PostfixBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GenerateViewCommand extends AbstractCommand {
    /**
     * Configure command.
     */
    protected function configure() {
        $this->setName('lampcp:postfix:generateview');
        $this->setDescription('Generate or drop the postfix table views');
        $this->addOption('create', 'c', InputOption::VALUE_NONE);
        $this->addOption('drop', 'd', InputOption::VALUE_NONE);
    }

    /**
     * Get sql files for method.
     *
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    protected function _getSqlFiles($method) {
        $fs   = new Filesystem();
        $sql  = array();
        $path = realpath(__DIR__ . '/../Resources/sql/' . strtolower($method));

        if (!$fs->exists($path)) {
            throw new \Exception('Cant find sql files for method!');
        }

        foreach (glob($path . '/*.sql') as $file) {
            $content = file_get_contents($file);

            if (!empty($content)) {
                $sql[] = $content;
            }
        }

        return $sql;
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Exception
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if ($input->getOption('create')) {
            $sql = $this->_getSqlFiles('create');

            foreach ($sql as $statement) {
                $this
                    ->_getDoctrine()
                    ->getConnection()
                    ->exec($statement);
            }

            $output->writeln('Views created.');

            return true;
        } elseif ($input->getOption('drop')) {
            $sql = $this->_getSqlFiles('drop');

            foreach ($sql as $statement) {
                $this
                    ->_getDoctrine()
                    ->getConnection()
                    ->exec($statement);
            }

            $output->writeln('Views deleted.');

            return true;
        } else {
            $output->writeln('Choose: --create or --drop');
        }

        return false;
    }
}
