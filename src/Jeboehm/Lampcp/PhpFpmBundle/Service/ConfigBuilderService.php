<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Jeboehm\Lampcp\PhpFpmBundle\Exception\NotMyConfigurationException;
use Jeboehm\Lampcp\PhpFpmBundle\Exception\DirectoryNotFoundException;
use Jeboehm\Lampcp\PhpFpmBundle\Model\PoolCreator;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\CoreBundle\Entity\DomainInterface;

/**
 * Class ConfigBuilderService
 *
 * Builds the PHP-FPM configuration.
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderService {
    /** Filename extension for pool configs. */
    const _FILEEXT_POOL = '.conf';

    /** @var TwigEngine */
    private $_twig;

    /** @var ConfigService */
    private $_configservice;

    /** @var EntityManager */
    private $_em;

    /**
     * Set Configservice.
     *
     * @param ConfigService $configservice
     *
     * @return ConfigBuilderService
     */
    public function setConfigservice($configservice) {
        $this->_configservice = $configservice;

        return $this;
    }

    /**
     * Get Configservice.
     *
     * @return ConfigService
     */
    public function getConfigservice() {
        return $this->_configservice;
    }

    /**
     * Set Entity Manager.
     *
     * @param EntityManager $em
     *
     * @return ConfigBuilderService
     */
    public function setEm($em) {
        $this->_em = $em;

        return $this;
    }

    /**
     * Get Entity Manager.
     *
     * @return EntityManager
     */
    public function getEm() {
        return $this->_em;
    }

    /**
     * Set Twig.
     *
     * @param TwigEngine $twig
     *
     * @return ConfigBuilderService
     */
    public function setTwig($twig) {
        $this->_twig = $twig;

        return $this;
    }

    /**
     * Get Twig.
     *
     * @return TwigEngine
     */
    public function getTwig() {
        return $this->_twig;
    }

    /**
     * Returns a list of PHP enabled domains and
     * subdomains.
     *
     * @return DomainInterface[]
     */
    protected function _getPhpEnabledDomainsAndSubdomains() {
        $repositoryDomain    = $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:Domain');
        $repositorySubdomain = $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:Subdomain');

        $domains    = $repositoryDomain->findBy(array('parsePhp' => true));
        $subdomains = $repositorySubdomain->findBy(array('parsePhp' => true));
        $list       = array_merge($domains, $subdomains);

        return $list;
    }

    /**
     * Returns a list of relevant users.
     * The list contains only users which have
     * PHP-enabled domains assigned.
     *
     * @return User[]
     */
    protected function _getRelevantUsers() {
        $users = new ArrayCollection();

        foreach ($this->_getPhpEnabledDomainsAndSubdomains() as $obj) {
            if (!$users->contains($obj->getUser())) {
                $users->add($obj->getUser());
            }
        }

        return $users;
    }

    /**
     * Find user by configuration filename.
     *
     * @param string $filename
     *
     * @return User
     * @throws NotMyConfigurationException
     */
    protected function _getUserByFilename($filename) {
        // When prefix is missing, throw an exception.
        if (stripos($filename, PoolCreator::POOL_PREFIX) === false) {
            throw new NotMyConfigurationException();
        }

        $search  = array(
            PoolCreator::POOL_PREFIX,
            PoolCreator::POOL_SUFFIX,
            self::_FILEEXT_POOL,
        );
        $replace = array(
            '',
        );

        $username = str_ireplace($search, $replace, $filename);

        // Try to find an user entity.
        $repository = $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:User');
        $user       = $repository->findOneBy(array('name' => $username));

        return $user;
    }

    /**
     * Get filename for pool configuration.
     *
     * @param string $poolname
     *
     * @return string
     */
    protected function _getFilename($poolname) {
        return strtolower($poolname) . self::_FILEEXT_POOL;
    }

    /**
     * Get new pool creator.
     *
     * @param User $user
     *
     * @return PoolCreator
     */
    protected function _getPoolCreator(User $user) {
        $creator = new PoolCreator($this->getTwig(), $this
            ->getConfigservice()
            ->getParameter('phpfpm.socketdir'), $user);

        return $creator;
    }

    /**
     * Create pool configuration for given user.
     *
     * @param User $user
     *
     * @return bool
     * @throws DirectoryNotFoundException
     */
    public function createPool(User $user) {
        $creator   = $this->_getPoolCreator($user);
        $filename  = $this->_getFilename($creator->getPoolName());
        $directory = $this->_getPoolDirectory();
        $path      = $directory . '/' . $filename;
        $config    = $creator->getPoolConfiguration();

        if (!is_dir($directory)) {
            throw new DirectoryNotFoundException($path);
        }

        if (is_readable($path)) {
            if (file_get_contents($path) === $config) {
                return true;
            }
        }

        file_put_contents($path, $config);

        return true;
    }

    /**
     * Get the pool directory.
     *
     * @return string
     */
    protected function _getPoolDirectory() {
        return $this
            ->getConfigservice()
            ->getParameter('phpfpm.pooldir');
    }

    /**
     * Create pool configuration for all users.
     */
    public function createPools() {
        foreach ($this->_getRelevantUsers() as $user) {
            $this->createPool($user);
        }
    }

    /**
     * Delete old pool configurations.
     */
    public function deleteOldPools() {
        $fs     = new Filesystem();
        $unlink = array();

        foreach ($this->_findConfigFiles() as $file) {
            /** @var $file SplFileInfo */
            try {
                $user = $this->_getUserByFilename($file);
            } catch (NotMyConfigurationException $e) {
                continue;
            }

            if ($user === null) {
                $unlink[] = $file->getPathname();
            }
        }

        $fs->remove($unlink);
    }

    /**
     * Get configuration file finder.
     *
     * @return Finder
     */
    protected function _findConfigFiles() {
        $finder = new Finder();
        $finder
            ->in($this->_getPoolDirectory())
            ->name('*' . self::_FILEEXT_POOL)
            ->depth(0)
            ->files();

        return $finder;
    }
}