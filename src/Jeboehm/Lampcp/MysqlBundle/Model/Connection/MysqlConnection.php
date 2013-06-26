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

use Doctrine\DBAL\Connection;

/**
 * Class MysqlConnection
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model\Connection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConnection implements ConnectionInterface
{
    /** @var Connection */
    private $connection;

    /**
     * Get connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set connection.
     *
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }
}
