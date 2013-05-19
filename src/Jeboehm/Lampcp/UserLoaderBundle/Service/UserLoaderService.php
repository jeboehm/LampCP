<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UserLoaderBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jboehm\Bundle\PasswdBundle\Model\Group;
use Jboehm\Bundle\PasswdBundle\Model\GroupService;
use Jboehm\Bundle\PasswdBundle\Model\PasswdService;
use Jboehm\Bundle\PasswdBundle\Model\User as SystemUser;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\User;

/**
 * Class UserLoaderService
 *
 * @package Jeboehm\Lampcp\UserLoaderBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class UserLoaderService
{
    /** @var EntityManager */
    private $_em;
    /** @var ConfigService */
    private $_config;
    /** @var PasswdService */
    private $_passwdservice;
    /** @var GroupService */
    private $_groupservice;

    /**
     * Load files.
     */
    public function loadFiles()
    {
        $this->_passwdservice = new PasswdService($this->getPasswdFilepath());
        $this->_groupservice  = new GroupService($this->getGroupFilepath());
    }

    /**
     * Get path to passwd filepath.
     *
     * @return string
     */
    protected function getPasswdFilepath()
    {
        return $this
            ->getConfig()
            ->getParameter('unix.passwdfile');
    }

    /**
     * Get Config.
     *
     * @return ConfigService
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set Config
     *
     * @param ConfigService $config
     *
     * @return $this
     */
    public function setConfig(ConfigService $config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * Get group filepath.
     *
     * @return string
     */
    protected function getGroupFilepath()
    {
        return $this
            ->getConfig()
            ->getParameter('unix.groupfile');
    }

    /**
     * Copy system users to local storage.
     */
    public function copyToLocal()
    {
        foreach ($this->_passwdservice->getAll() as $systemuser) {
            /** @var User $localuser */
            $localuser = $this
                ->getUserRepository()
                ->findOneBy(array('name' => $systemuser->getName()));

            if (!$localuser) {
                /** @var Group $group */
                $group     = $this->_groupservice->findOneBy(array('gid' => $systemuser->getGid()));
                $localuser = new User();
                $localuser
                    ->setName($systemuser->getName())
                    ->setUid($systemuser->getUid())
                    ->setGid($systemuser->getGid())
                    ->setGroupname($group->getName());

                $this->persistUser($localuser);
            } else {
                $changed = false;

                if ($localuser->getUid() !== $systemuser->getUid()) {
                    $localuser->setUid($systemuser->getUid());
                    $changed = true;
                }

                if ($localuser->getGid() !== $systemuser->getGid()) {
                    /** @var Group $group */
                    $group   = $this->_groupservice->findOneBy(array('gid' => $systemuser->getGid()));
                    $changed = true;

                    $localuser
                        ->setGid($systemuser->getGid())
                        ->setGroupname($group->getName());
                }

                if ($changed) {
                    $this->persistUser($localuser);
                }
            }
        }
    }

    /**
     * Get user repository.
     *
     * @return EntityRepository
     */
    protected function getUserRepository()
    {
        return $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:User');
    }

    /**
     * Get Em.
     *
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->_em;
    }

    /**
     * Set Em.
     *
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
    }

    /**
     * Persist user.
     *
     * @param User $user
     */
    protected function persistUser(User $user)
    {
        $this
            ->getEm()
            ->persist($user);

        $this
            ->getEm()
            ->flush();
    }

    /**
     * Remove obsolete local users.
     */
    public function removeObsoleteLocalUsers()
    {
        /** @var User[] $users */
        $users = $this
            ->getUserRepository()
            ->findAll();

        foreach ($users as $user) {
            /** @var SystemUser $systemuser */
            $systemuser = $this->_passwdservice->findOneBy(array('name' => $user->getName()));

            if (!$systemuser && $user
                ->getDomain()
                ->count() === 0
            ) {
                $this->removeUser($user);
            }
        }
    }

    /**
     * Remove user.
     *
     * @param User $user
     */
    protected function removeUser(User $user)
    {
        $this
            ->getEm()
            ->remove($user);

        $this
            ->getEm()
            ->flush();
    }
}
