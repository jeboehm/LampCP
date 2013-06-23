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
 * Class Database
 *
 * Database model.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Database
{
    /** @var string */
    private $name;
    /** @var User[] */
    private $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = array();
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add user.
     *
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Get users.
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
