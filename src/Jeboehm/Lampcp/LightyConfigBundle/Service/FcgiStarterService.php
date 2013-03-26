<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Jeboehm\Lampcp\CoreBundle\Utilities\ExecUtility;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\AbstractBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class FcgiStarterService
 *
 * Starts neccessary PHP-FCGI daemons.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FcgiStarterService extends AbstractBuilderService implements BuilderServiceInterface {
    /**
     * Checks a given socket
     *
     * @param string $socketPath
     *
     * @return bool
     */
    protected function _checkSocket($socketPath) {
        $fs = new Filesystem();

        if (!$fs->exists($socketPath)) {
            return false;
        }

        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        $res    = @socket_connect($socket, $socketPath);
        socket_close($socket);

        if ($res !== true) {
            return false;
        }

        return true;
    }

    /**
     * Checks, if php is needed for domain
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
     *
     * @return bool
     */
    protected function _checkPhpNeeded(Domain $domain) {
        $enablePhp = $domain->getParsePhp();

        if (!$enablePhp) {
            foreach ($domain->getSubdomain() as $subdomain) {
                /** @var $subdomain Subdomain */
                $enablePhp = $subdomain->getParsePhp();

                if ($enablePhp) {
                    break;
                }
            }
        }

        return $enablePhp;
    }

    /**
     * @return void
     */
    public function buildAll() {
        foreach ($this->_getAllDomains() as $domain) {
            /** @var $domain Domain */
            if (!$this->_checkPhpNeeded($domain)) {
                continue;
            } else {
                $cmd = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';

                if (!is_executable($cmd)) {
                    $this
                        ->_getLogger()
                        ->err('(LightyConfigBundle) Could not execute ' . $cmd);
                    continue;
                } else {
                    if (!$this->_checkSocket($domain->getPath() . '/tmp/php.socket')) {
                        $this
                            ->_getLogger()
                            ->info('(LightyConfigBundle) PHP-FCGI is not running! Starting...');
                        $exec = new ExecUtility();
                        $exec->exec($cmd);
                    }
                }
            }
        }
    }
}
