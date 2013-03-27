<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UpdateBundle\Listener;

use Jeboehm\Lampcp\UpdateBundle\Service\UpdateExecutor;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class KernelCacheWarmer
 *
 * Executes the Updater on cache warming.
 *
 * @package Jeboehm\Lampcp\UpdateBundle\Listener
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class KernelCacheWarmer implements CacheWarmerInterface {
    /** @var UpdateExecutor */
    protected $_updater;

    /**
     * Constructor
     *
     * @param UpdateExecutor $updater
     */
    public function __construct(UpdateExecutor $updater) {
        $this->_updater = $updater;
    }

    /**
     * This cache warmer is NOT optional, to ensure that all
     * updates will be executed.
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    public function isOptional() {
        return false;
    }

    /**
     * Call the Update Executor.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir) {
        $this->_updater->executeUpdates();
    }
}