<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Adapter;

use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;
use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\ConfigurationInterface;

/**
 * Interface AdapterInterface
 *
 * Adapter for database administration.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Adapter
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface AdapterInterface
{
    /**
     * Set configuration.
     *
     * @param ConfigurationInterface $config
     *
     * @return void
     */
    public function setConfiguration(ConfigurationInterface $config);

    /**
     * Create database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function createDatabase(Database $database);

    /**
     * Delete database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function deleteDatabase(Database $database);

    /**
     * Update database.
     *
     * @param Database $database
     *
     * @return bool
     */
    public function updateDatabase(Database $database);

    /**
     * Create user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function createUser(User $user);

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function deleteUser(User $user);

    /**
     * Update user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function updateUser(User $user);
}
