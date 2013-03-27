<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UpdateBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractUpdateProvider
 *
 * Provides abstract methods for update providers.
 *
 * @package Jeboehm\Lampcp\UpdateBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractUpdateProvider {
    /** @var ContainerInterface */
    private $_container;

    /** @var EntityManager */
    private $_em;

    /**
     * Get module name (unique, but can have multiple versions).
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get module's version number (unique per module!).
     *
     * @return float
     */
    abstract public function getVersion();

    /**
     * True, if the update should run on fresh installations.
     *
     * @return bool
     */
    abstract public function getRunOnFreshInstallations();

    /**
     * Execute the update
     *
     * @return bool
     */
    abstract public function executeUpdate();

    /**
     * Custom constructor
     */
    public function prepareUpdate() {
    }

    /**
     * Set service container
     *
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container) {
        $this->_container = $container;

        return $this;
    }

    /**
     * Get service container
     *
     * @return ContainerInterface
     */
    public function getContainer() {
        return $this->_container;
    }

    /**
     * Get entity manager
     *
     * @return EntityManager
     */
    public function getDoctrine() {
        return $this->_em;
    }

    /**
     * Set entity manager
     *
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setDoctrine(EntityManager $em) {
        $this->_em = $em;

        return $this;
    }
}