<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Update\RoleUpdate;

use Jeboehm\Lampcp\CoreBundle\Entity\Admin;
use Jeboehm\Lampcp\UpdateBundle\Model\AbstractUpdateProvider;

class PromoteAllSuperAdmin extends AbstractUpdateProvider {
    /**
     * Get module name (unique, but can have multiple versions).
     *
     * @return string
     */
    public function getName() {
        return __CLASS__;
    }

    /**
     * Get module's version number (unique per module!).
     *
     * @return float
     */
    public function getVersion() {
        return 1.0;
    }

    /**
     * True, if the update should run on fresh installations.
     *
     * @return bool
     */
    public function getRunOnFreshInstallations() {
        return true;
    }

    /**
     * Execute the update
     *
     * @return bool
     */
    public function executeUpdate() {
        $users = $this->_getAllUsers();

        foreach ($users as $user) {
            if (!$user->isSuperAdmin()) {
                $user->addRole($user::ROLE_SUPER_ADMIN);
            }
        }

        $this
            ->getDoctrine()
            ->flush();

        return true;
    }

    /**
     * Get all users
     *
     * @return Admin[]
     */
    protected function _getAllUsers() {
        $users = $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Admin')
            ->findAll();

        return $users;
    }
}