<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Model\Configuration;

/**
 * Interface ConfigurationInterface
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface ConfigurationInterface
{
    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get host.
     *
     * @return string
     */
    public function getHost();

    /**
     * Get port.
     *
     * @return int
     */
    public function getPort();
}
