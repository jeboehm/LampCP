<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Model;

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost as ParentVhost;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;

/**
 * Class Vhost
 *
 * Holds a virtual host.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Vhost extends ParentVhost {
    /**
     * Returns true, if the vhost is bound to all ips (*, 0.0.0.0)
     *
     * @return bool
     */
    public function getBoundToAllIps() {
        if ($this
            ->getIpaddress()
            ->getIp() === '*'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get protections.
     *
     * @return Protection[]
     */
    protected function _getProtection() {
        return $this->domain->getProtection();
    }

    /**
     * Get pathoptions.
     *
     * @return PathOption[]
     */
    protected function _getPathOption() {
        return $this->domain->getPathoption();
    }

    /**
     * Get directory options.
     *
     * @return array
     */
    public function getDirectoryOptions() {
        $options = parent::getDirectoryOptions();

        foreach ($options as $key => $value) {
            $pathOld = $value['path'];
            $pathNew = substr($pathOld, strlen($this->getDocumentRoot()));

            if (empty($pathNew)) {
                $pathNew = '/';
            }

            $value['path'] = $pathNew;
            $options[$key] = $value;
        }

        return $options;
    }

    /**
     * Get ServerName.
     *
     * @return string
     */
    public function getServerNameRegex() {
        $servername = $this->getServerName();
        $servername = str_replace('.', '\\.', $servername);
        $servername = sprintf('^(www\.)?%s$', $servername);

        return $servername;
    }
}
