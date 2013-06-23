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
 * Class MysqlConfiguration
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Model\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConfiguration implements ConfigurationInterface
{
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    private $host;
    /** @var int */
    private $port;

    /**
     * Get Host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set Host.
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get Password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set Password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get Port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set Port.
     *
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = intval($port);

        return $this;
    }

    /**
     * Get Username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set Username.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return '';
    }
}
