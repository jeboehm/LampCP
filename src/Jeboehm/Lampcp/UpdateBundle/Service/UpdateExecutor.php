<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UpdateBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\UpdateExecution;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\UpdateBundle\Model\AbstractUpdateProvider;

/**
 * Class UpdateExecutor
 *
 * Gets filled with update providers, checks
 * if the execution is neccessary and executes
 * them.
 *
 * @package Jeboehm\Lampcp\UpdateBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class UpdateExecutor {
    /** @var EntityManager */
    protected $_em;

    /** @var Logger */
    protected $_logger;

    /** @var ConfigService */
    protected $_configservice;

    /** @var ContainerInterface */
    protected $_container;

    /** @var array */
    protected $_provider;

    /**
     * Konstruktor.
     *
     * @param EntityManager      $em
     * @param Logger             $log
     * @param ConfigService      $cs
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, Logger $log, ConfigService $cs, ContainerInterface $container) {
        $this->_em            = $em;
        $this->_logger        = $log;
        $this->_configservice = $cs;
        $this->_container     = $container;
        $this->_provider      = array();
    }

    /**
     * Add provider.
     *
     * @param AbstractUpdateProvider $provider
     */
    public function addProvider(AbstractUpdateProvider $provider) {
        $this->_provider[] = $provider;
    }

    /**
     * Get UpdateExecution Repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function _getRepository() {
        return $this->_em->getRepository('JeboehmLampcpCoreBundle:UpdateExecution');
    }

    /**
     * Checks, if the given provider name and version is already executed.
     *
     * @param string $name
     * @param string $version
     *
     * @return bool
     */
    protected function _checkAlreadyExecuted($name, $version) {
        $result = $this
            ->_getRepository()
            ->findOneBy(array(
                             'name'    => $name,
                             'version' => $version,
                        ));

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Get filename of update provider.
     *
     * @param AbstractUpdateProvider $provider
     *
     * @return string
     */
    protected function _getFilenameOfProvider(AbstractUpdateProvider $provider) {
        $reflection = new \ReflectionClass(get_class($provider));

        return $reflection->getFileName();
    }

    /**
     * Check, if the given file is newer than the installation
     *
     * @param string $filename
     *
     * @return bool
     */
    protected function _checkFileNewerThanInstallation($filename) {
        $mtime = filemtime($filename);
        $itime = $this->_configservice->getParameter('core.installdate');

        return $mtime > $itime;
    }

    /**
     * Get all outstanding update providers.
     *
     * @return AbstractUpdateProvider[]
     */
    protected function _getOutstandingUpdateProviders() {
        $queue = array();

        foreach ($this->_provider as $provider) {
            /** @var $provider AbstractUpdateProvider */
            if (!$this->_checkAlreadyExecuted($provider->getName(), $provider->getVersion())) {
                if ($provider->getRunOnFreshInstallations()) {
                    // Just execute! :)
                    $queue[] = $provider;
                } else {
                    /**
                     * Compare installation date with filemtime of the provider.
                     * If the installation is newer than the filemtime, don't run
                     * the update.
                     */
                    $filename = $this->_getFilenameOfProvider($provider);

                    if ($this->_checkFileNewerThanInstallation($filename)) {
                        $queue[] = $provider;
                    }
                }
            }
        }

        return $queue;
    }

    /**
     * Prepares an update provider
     */
    protected function _prepareProvider(AbstractUpdateProvider $provider) {
        $provider
            ->setContainer($this->_container)
            ->setDoctrine($this->_em)
            ->prepareUpdate();
    }

    /**
     * Save execution time of an update provider
     *
     * @param AbstractUpdateProvider $provider
     */
    protected function _saveExecution(AbstractUpdateProvider $provider) {
        $exec = new UpdateExecution();
        $exec
            ->setName($provider->getName())
            ->setVersion($provider->getVersion())
            ->setExecutionTime(new \DateTime());

        $this->_em->persist($exec);
        $this->_em->flush();
    }

    /**
     * Execute all outstanding updates.
     */
    public function executeUpdates() {
        $providers = $this->_getOutstandingUpdateProviders();

        foreach ($providers as $provider) {
            $this->_prepareProvider($provider);

            $this->_logger->info(sprintf('(Updater) Found update: %s, Ver.: %s', $provider->getName(), $provider->getVersion()));

            if ($provider->executeUpdate()) {
                $this->_logger->info(sprintf('(Updater) Update successful: %s, Ver.: %s', $provider->getName(), $provider->getVersion()));
            } else {
                $this->_logger->info(sprintf('(Updater) Update failed: %s, Ver.: %s', $provider->getName(), $provider->getVersion()));
            }

            $this->_saveExecution($provider);
        }
    }
}