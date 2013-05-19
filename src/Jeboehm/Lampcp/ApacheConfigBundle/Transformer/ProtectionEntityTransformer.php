<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Transformer;

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection as Config;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection as Entity;
use Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser as EntityUser;

/**
 * Class ProtectionEntityTransformer
 *
 * Transform protection entities to protection config models.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionEntityTransformer
{
    /** authuser.passwd path */
    const path = '%s/conf/authuser_%s.passwd';

    /**
     * Transform to protection model.
     *
     * @param Entity $protection
     *
     * @return Config[]
     */
    public static function transform(Entity $protection)
    {
        $return = array();
        $path   = sprintf(
            self::path,
            $protection
                ->getDomain()
                ->getPath(),
            $protection->getId()
        );

        foreach ($protection->getProtectionuser() as $user) {
            /** @var EntityUser $user */
            $model = new Config();
            $model
                ->setUsername($user->getUsername())
                ->setPassword($user->getPassword())
                ->setPath($path)
                ->setId(
                    $user
                        ->getProtection()
                        ->getId()
                );

            $return[] = $model;
        }

        return $return;
    }
}