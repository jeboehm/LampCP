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
 * Class MysqlUserModel
 *
 * Holds a MySQL user.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlUserModel {
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $host;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->host = 'localhost';
    }

    /**
     * Set host.
     *
     * @param string $host
     *
     * @return MysqlUserModel
     */
    public function setHost($host) {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host.
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return MysqlUserModel
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return MysqlUserModel
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
}
