<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection as Model;
use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\ProtectionEntityTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser;

/**
 * Class ProtectionEntityTransformerTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ProtectionEntityTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provides protections.
     *
     * @return array
     */
    public function dataProvider()
    {
        $domain = new Domain();
        $domain->setPath('/var/www/test.de');

        $protection = new Protection($domain);
        $user1      = new ProtectionUser($domain, $protection);
        $user2      = new ProtectionUser($domain, $protection);

        $user1
            ->setUsername('test1')
            ->setPassword('pw1');

        $user2
            ->setUsername('test2')
            ->setPassword('pw2');

        $collection = new ArrayCollection(array($user1, $user2));
        $protection
            ->setProtectionuser($collection)
            ->setId(1)
            ->setPath('/var/www/test.de/htdocs/test');

        return array(
            array($protection),
        );
    }

    /**
     * Test transform.
     *
     * @param Protection $protection
     *
     * @dataProvider dataProvider
     */
    public function testTransform(Protection $protection)
    {
        $new = ProtectionEntityTransformer::transform($protection);

        $this->assertCount(
            $protection
                ->getProtectionuser()
                ->count(),
            $new
        );

        foreach ($protection->getProtectionuser() as $user) {
            /** @var ProtectionUser $user */
            $suche = array_search($user->getUsername(), $this->_collectUsernames($new));

            $this->assertInternalType('integer', $suche);
        }
    }

    /**
     * Collect usernames from Model array.
     *
     * @param Model[] $arr
     *
     * @return array
     */
    protected function _collectUsernames(array $arr)
    {
        $return = array();

        foreach ($arr as $model) {
            /** @var Model $model */
            $return[] = $model->getUsername();
        }

        return $return;
    }
}
