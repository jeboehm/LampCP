<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Model\Connection;

/**
 * Interface ConnectionInterface
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model\Connection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface ConnectionInterface
{
    /**
     * Get connection.
     *
     * @return mixed
     */
    public function getConnection();
}
