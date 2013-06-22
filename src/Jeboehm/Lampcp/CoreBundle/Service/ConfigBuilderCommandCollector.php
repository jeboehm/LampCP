<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Service;

use Jeboehm\Lampcp\CoreBundle\Command\ConfigBuilderCommandInterface;

/**
 * Class ConfigBuilderCommandCollector
 *
 * Collects the config builder commands.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderCommandCollector
{
    /** @var array */
    private $builder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->builder = array();
    }

    /**
     * Add ConfigBuilder.
     *
     * @param ConfigBuilderCommandInterface $builder
     *
     * @return $this
     */
    public function addBuilder(ConfigBuilderCommandInterface $builder)
    {
        $this->builder[] = $builder;

        return $this;
    }

    /**
     * Get all config builders.
     *
     * @return ConfigBuilderCommandInterface[]
     */
    public function getBuilders()
    {
        return $this->builder;
    }
}
