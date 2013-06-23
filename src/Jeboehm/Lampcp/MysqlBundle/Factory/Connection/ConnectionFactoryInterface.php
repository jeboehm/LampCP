<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Factory\Connection;

use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\ConfigurationInterface;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\ConnectionInterface;

/**
 * Interface ConnectionFactoryInterface
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Factory\Connection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface ConnectionFactoryInterface
{
    /**
     * Get connection.
     *
     * @return ConnectionInterface
     */
    public function factory();

    /**
     * Set configuration.
     *
     * @param ConfigurationInterface $config
     */
    public function setConfiguration(ConfigurationInterface $config);
}
