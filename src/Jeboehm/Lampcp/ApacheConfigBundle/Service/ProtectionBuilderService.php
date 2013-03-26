<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection as ProtectionConfigModel;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection as ProtectionEntity;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;

/**
 * Class ProtectionBuilderService
 *
 * Builds the protections.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionBuilderService extends AbstractBuilderService implements BuilderServiceInterface {
    const _twigAuthUserFile = 'JeboehmLampcpApacheConfigBundle:Apache2:AuthUserFile.conf.twig';

    /**
     * Get protection model array
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Protection $protection
     *
     * @return ProtectionConfigModel[]
     */
    protected function _getProtectionModelArray(ProtectionEntity $protection) {
        $models = array();

        foreach ($protection->getProtectionuser() as $prot) {
            $mod = new ProtectionConfigModel();
            $mod
                ->setId($prot->getId())
                ->setUsername($prot->getUsername())
                ->setPassword($this
                    ->_getCryptService()
                    ->decrypt($prot->getPassword()));

            $models[] = $mod;
        }

        return $models;
    }

    /**
     * Generate AuthUserFile
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Protection $protection
     *
     * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
     */
    protected function _generateAuthUserFile(ProtectionEntity $protection) {
        $fs               = new Filesystem();
        $models           = $this->_getProtectionModelArray($protection);
        $contents         = $this->_renderTemplate(self::_twigAuthUserFile, array(
                                                                                 'users' => $models,
                                                                            ));
        $pathAuthUserFile = sprintf('%s/conf/authuser_%s.passwd', $protection
            ->getDomain()
            ->getPath(), $protection->getId());

        if (!is_writable(dirname($pathAuthUserFile))) {
            throw new CouldNotWriteFileException();
        }

        $this
            ->_getLogger()
            ->info('(ApacheConfigBundle) Generating AuthUserFile:' . $pathAuthUserFile);
        file_put_contents($pathAuthUserFile, $contents);

        // Change rights
        $fs->chmod($pathAuthUserFile, 0440);

        // Change user & group
        $fs->chown($pathAuthUserFile, $protection
            ->getDomain()
            ->getUser()
            ->getName());
        $fs->chgrp($pathAuthUserFile, $protection
            ->getDomain()
            ->getUser()
            ->getGroupname());
    }

    /**
     * Build all configurations
     */
    public function buildAll() {
        foreach ($this->_getAllDomains() as $domain) {
            foreach ($domain->getProtection() as $protection) {
                $this->_generateAuthUserFile($protection);
            }
        }

        $this->_cleanConfDirectory();
    }

    /**
     * Look for obsolete AuthUserFile files
     */
    protected function _cleanConfDirectory() {
        /** @var $domains Domain[] */
        $fs      = new Filesystem();
        $domains = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Domain')
            ->findAll();

        foreach ($domains as $domain) {
            $dir   = $domain->getPath() . '/conf';
            $files = glob($dir . '/authuser_*.passwd');

            foreach ($files as $filepath) {
                $idStart = strpos($filepath, 'authuser_') + strlen('authuser_');
                $idEnd   = strpos($filepath, '.passwd');
                $id      = intval(substr($filepath, $idStart, ($idEnd - $idStart)));

                $protection = $this
                    ->_getDoctrine()
                    ->getRepository('JeboehmLampcpCoreBundle:Protection')
                    ->findOneBy(array(
                                     'id'     => $id,
                                     'domain' => $domain->getId(),
                                ));

                if (!$protection) {
                    $this
                        ->_getLogger()
                        ->info('(ApacheConfigBundle) Deleting obsolete AuthUserFile: ' . $filepath);
                    $fs->remove($filepath);
                }
            }
        }
    }
}
