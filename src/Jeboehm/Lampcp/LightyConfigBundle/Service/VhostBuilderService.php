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

use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService as ParentVhostBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\LightyConfigBundle\Model\Vhost;

/**
 * Class VhostBuilderService
 *
 * Builds the Lighttpd configuration.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderService extends ParentVhostBuilderService implements BuilderServiceInterface {
    const vhostConfigTemplate = 'JeboehmLampcpLightyConfigBundle:Lighttpd:vhost.conf.twig';

    /**
     * Save vhost config.
     *
     * @param string $content
     *
     * @throws CouldNotWriteFileException
     * @return void
     */
    protected function _saveVhostConfig($content) {
        $target = $this
            ->_getConfigService()
            ->getParameter('lighttpd.pathlighttpdconf') . '/' . self::vhostConfigFilename;

        $content = str_replace('  ', '', $content);
        $content = str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $content);

        if (!is_writable(dirname($target))) {
            throw new CouldNotWriteFileException();
        }

        file_put_contents($target, $content);
    }

    /**
     * Build all configurations.
     */
    public function buildAll() {
        $content = $this->_renderTemplate(self::vhostConfigTemplate, array(
                                                                 'defaultcert' => $this->_getSingleCertificateWithDomainsAssigned(),
                                                                 'vhosts'      => $this->_getVhostModels(),
                                                                 'ips'         => $this->_getAllIpAddresses(),
                                                            ));

        $this->_saveVhostConfig($content);
    }

    /**
     * Get single certificate with domain / subdomain set.
     *
     * @return Certificate
     */
    protected function _getSingleCertificateWithDomainsAssigned() {
        /** @var $certs Certificate[] */
        $certs = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Certificate')
            ->findAll();

        foreach ($certs as $certificate) {
            /** @var $certificate Certificate */
            if ($certificate
                ->getDomain()
                ->count() > 0 || $certificate
                ->getSubdomain()
                ->count() > 0
            ) {
                return $certificate;
            }
        }

        return null;
    }

    /**
     * Get new Vhost model.
     *
     * @return Vhost
     */
    protected function _getVhost() {
        return new Vhost();
    }
}
