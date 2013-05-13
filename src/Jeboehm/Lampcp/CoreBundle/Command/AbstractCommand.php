<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class AbstractCommand
 *
 * Provides some useful methods for commands.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractCommand extends ContainerAwareCommand {
    /** @var ConfigService */
    private $_configService;

    /** @var Logger */
    private $_logger;

    /**
     * Get doctrine.
     *
     * @return EntityManager
     */
    protected function _getDoctrine() {
        return $this
            ->getContainer()
            ->get('doctrine.orm.entity_manager');
    }

    /**
     * Get system config service.
     *
     * @return ConfigService
     */
    protected function _getConfigService() {
        return $this
            ->getContainer()
            ->get('config');
    }

    /**
     * Get logger.
     *
     * @return Logger
     */
    protected function _getLogger() {
        return $this
            ->getContainer()
            ->get('logger');
    }
}
