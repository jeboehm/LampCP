<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\MysqlBundle\Model\User;

/**
 * Class UserCollection
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Collection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class UserCollection extends ArrayCollection
{
    /**
     * Find user model by name.
     *
     * @param string $name
     *
     * @return User|null
     */
    public function findByName($name)
    {
        foreach ($this->getIterator() as $user) {
            /** @var User $user */
            if ($user->getName() === $name) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Find users whose names begin with $start.
     *
     * @param string $start
     *
     * @return UserCollection
     */
    public function findByNameStartsWith($start)
    {
        $return = array();

        foreach ($this->getIterator() as $user) {
            /** @var User $user */
            if (substr($user->getName(), 0, strlen($start)) === $start) {
                $return[] = $user;
            }
        }

        return new UserCollection($return);
    }
}
