<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Command;

use Jeboehm\Lampcp\SetupBundle\Model\Transformer\ParametersYamlTransformer;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\ValidationResult;
use Jeboehm\Lampcp\SetupBundle\Service\FormGeneratorService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AbstractCommand
 *
 * @package Jeboehm\Lampcp\SetupBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /** @var OutputInterface */
    private $_output;

    /**
     * Get parameters.yml transformer.
     *
     * @return ParametersYamlTransformer
     */
    protected function getParametersYamlTransformer()
    {
        /** @var ParametersYamlTransformer $transformer */
        $transformer = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.transformer.parametersyamltransformer');

        return $transformer;
    }

    /**
     * Get form generator service.
     *
     * @return FormGeneratorService
     */
    protected function getFormGenerator()
    {
        /** @var FormGeneratorService $service */
        $service = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.formgeneratorservice');

        $service
            ->setOutput($this->getOutput())
            ->setDialog($this->getDialogHelper());

        return $service;
    }

    /**
     * Get Output
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Get dialog helper.
     *
     * @return DialogHelper
     */
    protected function getDialogHelper()
    {
        return $this
            ->getHelperSet()
            ->get('dialog');
    }

    /**
     * Check result. Output error message.
     *
     * @param ValidationResult $result
     *
     * @return bool
     */
    protected function checkResult(ValidationResult $result)
    {
        if (!$result->getSuccessful()) {
            if (!is_array($result->getMessage())) {
                $messages = array($result->getMessage());
            } else {
                $messages = $result->getMessage();
            }

            foreach ($messages as $message) {
                $this
                    ->getOutput()
                    ->writeln('<error>' . $message . '</error>');
            }

            return false;
        }

        return true;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
    }

    /**
     * Set Output
     *
     * @param OutputInterface $output
     *
     * @return $this
     */
    private function setOutput(OutputInterface $output)
    {
        $this->_output = $output;

        return $this;
    }

    /**
     * Run console command.
     *
     * @param array $command
     */
    protected function runConsoleCommand(array $command)
    {
        /** @var Kernel $kernel */
        $kernel = $this
            ->getContainer()
            ->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->run(new ArrayInput($command));
    }
}
