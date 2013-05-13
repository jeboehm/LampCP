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

use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\ProtectionEntityTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ProtectionBuilderService
 *
 * Build the username/password files to protect http directories.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionBuilderService
{
    /** authuser.passwd template */
    const template_authuser = 'JeboehmLampcpApacheConfigBundle:Apache2:authuser.passwd.twig';
    /** @var TwigEngine */
    private $_twigEngine;
    /** @var CryptService */
    private $_cryptservice;

    /**
     * Create auth user file for given protection.
     *
     * @param Protection $protection
     */
    public function createAuthUserFile(Protection $protection)
    {
        $path = sprintf(
            '%s/conf/authuser_%s.passwd',
            $protection
                ->getDomain()
                ->getPath(),
            $protection->getId()
        );

        $content = $this->renderAuthUserFile($this->transformEntity($protection));

        file_put_contents($path, $content);
    }

    /**
     * Render auth-user file.
     *
     * @param array $models
     *
     * @return string
     */
    public function renderAuthUserFile(array $models)
    {
        $content = $this
            ->getTwigEngine()
            ->render(
                self::template_authuser,
                array(
                     'users' => $models,
                )
            );

        return $content;
    }

    /**
     * Get Twig Engine.
     *
     * @return TwigEngine
     */
    public function getTwigEngine()
    {
        return $this->_twigEngine;
    }

    /**
     * Set TwigEngine.
     *
     * @param TwigEngine $twigEngine
     *
     * @return $this
     */
    public function setTwigEngine(TwigEngine $twigEngine)
    {
        $this->_twigEngine = $twigEngine;

        return $this;
    }

    /**
     * Transform protection entity.
     *
     * @param Protection $protection
     *
     * @return \Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection[]
     */
    public function transformEntity(Protection $protection)
    {
        $arr = ProtectionEntityTransformer::transform($protection);

        foreach ($arr as $model) {
            try {
                $password = $this
                    ->getCryptservice()
                    ->decrypt($model->getPassword());
                $model->setPassword($password);
            } catch (\Exception $e) {
            }
        }

        return $arr;
    }

    /**
     * Get Cryptservice.
     *
     * @return CryptService
     */
    public function getCryptservice()
    {
        return $this->_cryptservice;
    }

    /**
     * Set Cryptservice.
     *
     * @param CryptService $cryptservice
     *
     * @return $this
     */
    public function setCryptservice(CryptService $cryptservice)
    {
        $this->_cryptservice = $cryptservice;

        return $this;
    }

    /**
     * Remove obsolete auth-user files.
     *
     * @param Domain $domain
     */
    public function removeObsoleteAuthUserFiles(Domain $domain)
    {
        $finder = new Finder();
        $fs     = new Filesystem();
        $files  = array();
        $ids    = array();

        foreach ($domain->getProtection() as $protection) {
            /** @var Protection $protection */
            $ids[] = $protection->getId();
        }

        $finder
            ->in($domain->getPath() . '/conf')
            ->name('authuser_*.passwd')
            ->depth(0)
            ->files();

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $id = $this->getIdFromFilename($file->getFilename());
            if ($id === null) {
                continue;
            }

            if (!in_array($id, $ids)) {
                // Obsolete file.
                $files[] = $file->getPathname();
            }
        }

        if (count($files) > 0) {
            $fs->remove($files);
        }
    }

    /**
     * Extract ID from Filename.
     *
     * authuser_39.passwd -> 39
     *
     * @param string $filename
     *
     * @return int|null
     */
    public function getIdFromFilename($filename)
    {
        $replaced = str_replace(
            array(
                 'authuser_',
                 '.passwd',
            ),
            '',
            $filename
        );

        $id = intval($replaced);

        if (!empty($id)) {
            return $id;
        }

        return null;
    }
}