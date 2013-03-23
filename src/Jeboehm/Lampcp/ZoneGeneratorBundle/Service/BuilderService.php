<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer\ZonefileTransformer;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\DirectoryNotFound;

class BuilderService {
    /** @var \Doctrine\ORM\EntityManager */
    protected $_em;

    /** @var \Symfony\Bridge\Monolog\Logger */
    protected $_logger;

    /** @var \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService */
    protected $_config;

    /**
     * Konstruktor
     *
     * @param \Doctrine\ORM\EntityManager                        $em
     * @param \Symfony\Bridge\Monolog\Logger                     $logger
     * @param \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService $config
     */
    public function __construct(EntityManager $em, Logger $logger, ConfigService $config) {
        $this->_em     = $em;
        $this->_logger = $logger;
        $this->_config = $config;
    }

    /**
     * Get zone directory
     *
     * @return string
     */
    protected function _getZoneDirectory() {
        return $this->_config->getParameter('dns.config.zonedir');
    }

    /**
     * Get zonefile definition path
     *
     * @return string
     */
    protected function _getZoneDefinitionPath() {
        return $this->_config->getParameter('dns.config.zonedef');
    }

    /**
     * Check requirements
     *
     * @throws \Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\DirectoryNotFound
     */
    protected function _checkRequirements() {
        $fs = new Filesystem();
        $fs->mkdir($this->_getZoneDirectory(), 0755);

        if (!$fs->exists(array($this->_getZoneDefinitionPath(), $this->_getZoneDirectory()))) {
            throw new DirectoryNotFound();
        }
    }

    /**
     * Get zone files definition
     *
     * @param string $name
     * @param string $dbPath
     *
     * @return string
     */
    protected function _getZoneDefinition($name, $dbPath) {
        $text = <<< EOT
zone "%name%" IN {
    type master;
    file "%path%";
};


EOT;

        $text = str_replace(array(
                                 '%name%',
                                 '%path%',
                            ), array(
                                    $name,
                                    $dbPath,
                               ), $text);

        return $text;
    }

    /**
     * Get Dns Entities
     *
     * @return Dns[]
     */
    protected function _getDnsEntities() {
        /** @var $repository EntityRepository */
        $repository = $this->_em->getRepository('JeboehmLampcpCoreBundle:Dns');

        return $repository->findAll();
    }

    /**
     * Check for zone definition in config file
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Dns $zone
     *
     * @return bool
     */
    protected function _checkZoneDefinition(Dns $zone) {
        $dbPath     = sprintf('%s/%s.%s', $this->_getZoneDirectory(), $zone->getOrigin(), 'db');
        $config     = file_get_contents($this->_getZoneDefinitionPath());
        $definition = $this->_getZoneDefinition($zone->getOrigin(), $dbPath);

        if (strpos($config, $definition) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Build configuration
     */
    public function build() {
        $this->_checkRequirements();
        $dns            = $this->_getDnsEntities();
        $zoneDefinition = array();

        foreach ($dns as $entity) {
            $pathZoneDb       = sprintf('%s/%s.%s', $this->_getZoneDirectory(), $entity->getOrigin(), 'db');
            $zoneDefinition[] = $this->_getZoneDefinition($entity->getOrigin(), $pathZoneDb);
            $transformer      = new ZonefileTransformer($entity->getZonecollection());

            file_put_contents($pathZoneDb, $transformer->transform());
        }

        file_put_contents($this->_getZoneDefinitionPath(), join(PHP_EOL . PHP_EOL, $zoneDefinition));
    }
}
