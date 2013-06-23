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

use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * Class AbstractConfigLoader
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Factory\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractConfigLoader
{
    /** @var ConfigService */
    private $config_service;

    /**
     * Constructor.
     *
     * @param ConfigService $cs
     */
    public function __construct(ConfigService $cs)
    {
        $this->config_service = $cs;
    }

    /**
     * Get configuration service.
     *
     * @return ConfigService
     */
    public function getConfigService()
    {
        return $this->config_service;
    }

    /**
     * Get the configuration model.
     *
     * @return ConfigurationInterface
     */
    abstract public function factory();
}
