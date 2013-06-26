<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Factory\Configuration;

use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\MysqlConfiguration;

/**
 * Class MysqlConfigLoader
 *
 * Loads the mysql configuration.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Factory\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConfigLoader extends AbstractConfigLoader
{
    /**
     * Get the configuration model.
     *
     * @return MysqlConfiguration
     */
    public function factory()
    {
        $config = new MysqlConfiguration();
        $config
            ->setHost(
                $this
                    ->getConfigService()
                    ->getParameter('mysql.host')
            )
            ->setUsername(
                $this
                    ->getConfigService()
                    ->getParameter('mysql.rootuser')
            )
            ->setPassword(
                $this
                    ->getConfigService()
                    ->getParameter('mysql.rootpassword')
            )
            ->setPort(
                $this
                    ->getConfigService()
                    ->getParameter('mysql.port')
            );

        return $config;
    }
}
