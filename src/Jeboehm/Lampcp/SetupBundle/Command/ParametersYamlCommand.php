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

use Jeboehm\Lampcp\SetupBundle\Model\Exception\AbortException;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Crypto;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Database;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Misc;
use Jeboehm\Lampcp\SetupBundle\Model\Transformer\ParametersYamlTransformer;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\CryptoValidator;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\DatabaseValidator;
use Jeboehm\Lampcp\SetupBundle\Model\Validator\MiscValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ParametersYamlCommand
 *
 * @package Jeboehm\Lampcp\SetupBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ParametersYamlCommand extends AbstractCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('lampcp:setup:parameters')
            ->setDescription('Sets the parameters.yml up.');
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
        parent::execute($input, $output);

        try {
            $this->showIntro();
        } catch (AbortException $e) {
            return;
        }

        $transformer = $this->getParametersYamlTransformer();
        $database    = $this->_configureDatabase();
        $crypto      = $this->_configureCrypto();
        $misc        = $this->_configureMisc();

        $transformer
            ->addForm($database)
            ->addForm($crypto)
            ->addForm($misc);

        try {
            $output->writeln('Generating parameters.yml...');

            $this->generateParametersYaml($transformer);

            $output->writeln('<info>parameters.yml created successfully.</info>');
        } catch (IOException $e) {
            $output->writeln(sprintf('Could not save configuration: "%s"', $e->getMessage()));

            return;
        }

        $output->writeln('<info>Run lampcp "lampcp:setup:vhost" to create a vhost.</info>');

        return;
    }

    /**
     * Show intro.
     *
     * @throws AbortException
     */
    protected function showIntro()
    {
        $this
            ->getOutput()
            ->writeln(
                array(
                     '==================',
                     '== LampCP Setup ==',
                     '==================',
                     '',
                     $this->getReadme(),
                )
            );

        $result = $this
            ->getDialogHelper()
            ->askConfirmation(
                $this->getOutput(),
                '<info>This will configure LampCP. Continue? '
            );

        if (!$result) {
            throw new AbortException();
        }
    }

    /**
     * Get readme.
     *
     * @return null|string
     */
    protected function getReadme()
    {
        $path = realpath(__DIR__ . '/../../CoreBundle/Resources/misc/README.md');

        if (!empty($path) && is_readable($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    /**
     * Database setup.
     *
     * @return Database
     */
    protected function _configureDatabase()
    {
        $this
            ->getOutput()
            ->writeln('<info>Configure the database:</info>');

        /** @var DatabaseValidator $validator */
        $validator = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.validator.database');
        $database  = new Database();

        $this
            ->getFormGenerator()
            ->processForm($database);

        if (!$this->checkResult($validator->validate($database))) {
            return $this->_configureDatabase();
        }

        return $database;
    }

    /**
     * Configure crypto settings.
     *
     * @return Crypto
     */
    protected function _configureCrypto()
    {
        $this
            ->getOutput()
            ->writeln('<info>Configure application passwords:</info>');

        /** @var CryptoValidator $validator */
        $validator = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.validator.crypto');
        $crypto    = new Crypto();

        $this
            ->getFormGenerator()
            ->processForm($crypto);

        if (!$this->checkResult($validator->validate($crypto))) {
            return $this->_configureCrypto();
        }

        return $crypto;
    }

    /**
     * Misc setup.
     *
     * @return Misc
     */
    protected function _configureMisc()
    {
        $this
            ->getOutput()
            ->writeln('<info>Configure misc settings:</info>');

        /** @var MiscValidator $validator */
        $validator = $this
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.validator.misc');
        $misc      = new Misc();

        $this
            ->getFormGenerator()
            ->processForm($misc);

        if (!$this->checkResult($validator->validate($misc))) {
            return $this->_configureMisc();
        }

        return $misc;
    }

    /**
     * Writes the parameters.yml.
     *
     * @param ParametersYamlTransformer $transformer
     */
    protected function generateParametersYaml(ParametersYamlTransformer $transformer)
    {
        $fs      = new Filesystem();
        $content = $transformer->renderYaml();
        $path    = __DIR__ . '/../../../../../app/config/parameters.yml';

        if (!$fs->exists($path)) {
            $fs->touch($path);
        }

        file_put_contents($path, $content);
    }
}
