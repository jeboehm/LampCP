<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Jeboehm\Lampcp\CoreBundle\Service\ChangeTrackingService;
use Jeboehm\Lampcp\CoreBundle\Command\AbstractCommand;
use Jeboehm\Lampcp\CoreBundle\Service\CronService;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Service\BuilderService;

class GenerateConfigCommand extends AbstractCommand {
    /**
     * Get watched entitys
     *
     * @return array
     */
    protected function _getEntitys() {
        $entitys = array(
            'Jeboehm\Lampcp\CoreBundle\Entity\Dns',
        );

        return $entitys;
    }

    /**
     * Configure command
     */
    protected function configure() {
        $this->setName('lampcp:zone:generateconfig');
        $this->setDescription('Generates the zonefiles');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
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
        $run = false;

        if ($input->getOption('force') || $this->_isChanged()) {
            $run = true;
        }

        if ($run) {
            $this->_getLogger()->info('(ZoneGeneratorBundle) Executing...');

            if ($input->getOption('verbose')) {
                $output->writeln('(ZoneGeneratorBundle) Executing...');
            }

            try {
                $builder = $this->_getBuilderService();
                $builder->build();
            } catch (\Exception $e) {
                $this->_getLogger()->err('(ZoneGeneratorBundle) Error: ' . $e->getMessage());

                throw $e;
            }
        } else {
            if ($input->getOption('verbose')) {
                $output->writeln('(ZoneGeneratorBundle) No changes detected.');
            }
        }
    }

    /**
     * Checks for changed entitys that are relevant for this task
     *
     * @return bool
     */
    protected function _isChanged() {
        $last = $this->_getCronService()->getLastRun($this->getName());

        /**
         * First run
         */
        if (!$last) {
            return true;
        } else {
            /**
             * Find entities newer than $last
             */
            foreach ($this->_getEntitys() as $entity) {
                $result = $this->_getChangeTrackingService()->findNewer($entity, $last);

                if (count($result) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get builder service
     *
     * @return BuilderService
     */
    protected function _getBuilderService() {
        /** @var $service BuilderService */
        $service = $this->getContainer()->get('jeboehm_lampcp_zonegenerator.builderservice');

        return $service;
    }

    /**
     * Get change tracking service
     *
     * @return ChangeTrackingService
     */
    protected function _getChangeTrackingService() {
        return $this->getContainer()->get('jeboehm_lampcp_core.changetrackingservice');
    }

    /**
     * Get cron service
     *
     * @return CronService
     */
    protected function _getCronService() {
        return $this->getContainer()->get('jeboehm_lampcp_core.cronservice');
    }
}
