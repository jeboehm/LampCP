<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Model;

/**
 * Class Protection
 *
 * Holds a protection, crypts the password.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Protection {
    /** @var integer */
    private $id;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Protection
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return Protection
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

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Protection
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
        return crypt($this->password, base64_encode($this->password));
    }
}
