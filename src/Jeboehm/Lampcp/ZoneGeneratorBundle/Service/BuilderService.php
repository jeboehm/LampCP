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
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\DirectoryNotFound;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer\ZonefileTransformer;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ZoneDefinition;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BuilderService
 *
 * Builds Bind zonefiles.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class BuilderService {
    /** @var EntityManager */
    protected $_em;

    /** @var Logger */
    protected $_logger;

    /** @var ConfigService */
    protected $_config;

    /**
     * Konstruktor
     *
     * @param EntityManager $em
     * @param Logger        $logger
     * @param ConfigService $config
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
        $definition = ZoneDefinition::create($zone->getOrigin(), $dbPath);

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
            $zoneDefinition[] = ZoneDefinition::create($entity->getOrigin(), $pathZoneDb);
            $transformer      = new ZonefileTransformer($entity->getZonecollection());

            file_put_contents($pathZoneDb, $transformer->transform());
        }

        file_put_contents($this->_getZoneDefinitionPath(), join(PHP_EOL . PHP_EOL, $zoneDefinition));
    }
}
