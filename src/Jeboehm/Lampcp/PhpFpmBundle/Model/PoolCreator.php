<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Model;

use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\PhpFpmBundle\Exception\DirectoryNotFoundException;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * Class PoolCreator
 *
 * Creates a PHP-FPM pool configuration file.
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class PoolCreator
{
    /** Location of the configuration template. */
    const CFG_POOL_TEMPLATE = 'JeboehmLampcpPhpFpmBundle:pool:pool.conf.twig';

    /** Socket filename prefix. */
    const SOCKET_PREFIX = 'lampcp-php-fpm-';

    /** Socket filename suffix. */
    const SOCKET_SUFFIX = '.sock';

    /** Pool name prefix. */
    const POOL_PREFIX = 'LAMPCP-POOL-';

    /** Pool name suffix. */
    const POOL_SUFFIX = '';

    /** @var TwigEngine */
    private $_twig;

    /** @var string */
    private $_socketpath;

    /** @var User */
    private $_user;

    /**
     * Constructor.
     *
     * @param TwigEngine $twig
     * @param string     $socketpath
     * @param User       $user
     */
    public function __construct(TwigEngine $twig, $socketpath, User $user)
    {
        $this->_twig       = $twig;
        $this->_socketpath = $socketpath;
        $this->_user       = $user;
    }

    /**
     * Get the path of pool's socket.
     *
     * @return string
     * @throws DirectoryNotFoundException
     */
    public function getSocketPath()
    {
        if (!is_dir($this->_socketpath)) {
            throw new DirectoryNotFoundException($this->_socketpath);
        }

        $filename = self::SOCKET_PREFIX . $this->_user->getName() . self::SOCKET_SUFFIX;
        $path     = $this->_socketpath . '/' . $filename;

        return $path;
    }

    /**
     * Get pool name.
     *
     * @return string
     */
    public function getPoolName()
    {
        $name = self::POOL_PREFIX . $this->_user->getName() . self::POOL_SUFFIX;

        return $name;
    }

    /**
     * Render and return the pool configuration.
     *
     * @return string
     */
    public function getPoolConfiguration()
    {
        $vars = array(
            'poolname' => $this->getPoolName(),
            'user'     => $this->_user->getName(),
            'group'    => $this->_user->getGroupname(),
            'socket'   => $this->getSocketPath(),
        );
        $file = $this->_twig->render(self::CFG_POOL_TEMPLATE, $vars);

        return $file;
    }
}
