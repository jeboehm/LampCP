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
use Jeboehm\Lampcp\MysqlBundle\Model\Database;

/**
 * Class DatabaseCollection
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Collection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DatabaseCollection extends ArrayCollection
{
    /**
     * Find database model by name.
     *
     * @param string $name
     *
     * @return Database|null
     */
    public function findByName($name)
    {
        foreach ($this->getIterator() as $database) {
            /** @var Database $database */
            if ($database->getName() === $name) {
                return $database;
            }
        }

        return null;
    }

    /**
     * Find database whose names begin with $start.
     *
     * @param string $start
     *
     * @return DatabaseCollection
     */
    public function findByNameStartsWith($start)
    {
        $return = array();

        foreach ($this->getIterator() as $database) {
            /** @var Database $database */
            if (substr($database->getName(), 0, strlen($start)) === $start) {
                $return[] = $database;
            }
        }

        return new DatabaseCollection($return);
    }
}
