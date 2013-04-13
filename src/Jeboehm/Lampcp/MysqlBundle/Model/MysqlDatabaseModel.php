<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Model;

/**
 * Class MysqlDatabaseModel
 *
 * Holds a MySQL database.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlDatabaseModel {
    /** @var string */
    private $name;

    /** @var MysqlUserModel[] */
    private $users;

    /**
     * Konstruktor
     */
    public function __construct() {
        $this->users = array();
    }

    /**
     * Set database name.
     *
     * @param string $name
     *
     * @return MysqlDatabaseModel
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get database name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set permitted users.
     *
     * @param array $users
     *
     * @return MysqlDatabaseModel
     */
    public function setUsers($users) {
        $this->users = $users;

        return $this;
    }

    /**
     * Get permitted users.
     *
     * @return MysqlUserModel[]
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Get MySQL database permissions.
     *
     * @return array
     */
    public function getPermission() {
        $perm = array(
            'SELECT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'CREATE',
            'DROP',
            'INDEX',
            'ALTER',
            'CREATE TEMPORARY TABLES',
            'LOCK TABLES',
            'CREATE VIEW',
            'SHOW VIEW',
        );

        return $perm;
    }
}
