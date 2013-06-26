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

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\MysqlConnection;
use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\ConfigurationInterface;

/**
 * Class MysqlConnectionFactory
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Factory\Connection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConnectionFactory implements ConnectionFactoryInterface
{
    /** @var ConnectionFactory */
    private $factory;
    /** @var ConfigurationInterface */
    private $config;

    /**
     * Constructor.
     *
     * @param ConnectionFactory $factory
     */
    public function __construct(ConnectionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Set configuration.
     *
     * @param ConfigurationInterface $config
     */
    public function setConfiguration(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Get connection.
     *
     * @return MysqlConnection
     */
    public function factory()
    {
        $conn = new MysqlConnection();
        $conn->setConnection(
            $this->factory->createConnection(
                array(
                     'driver'   => 'pdo_mysql',
                     'user'     => $this->config->getUsername(),
                     'password' => $this->config->getPassword(),
                     'host'     => $this->config->getHost(),
                )
            )
        );

        return $conn;
    }
}
