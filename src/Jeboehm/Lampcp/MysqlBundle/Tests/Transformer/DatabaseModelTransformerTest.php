<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Transformer;

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\MysqlBundle\Model\User;
use Jeboehm\Lampcp\MysqlBundle\Transformer\DatabaseModelTransformer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DatabaseModelTransformerTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DatabaseModelTransformerTest extends WebTestCase
{
    /** @var DatabaseModelTransformer */
    protected $transformer;

    /**
     * Set up.
     */
    public function setUp()
    {
        $client = $this->createClient();

        $this->transformer = $client
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.transformer.databasemodeltransformer');
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProvider()
    {
        /** @var CryptService $cs */
        $cs = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_core.cryptservice');

        $domain = new Domain();
        $domain->setDomain('test.de');

        $a = new MysqlDatabase($domain);
        $a
            ->setName('lampcpsql1')
            ->setPassword('test123')
            ->setComment('test');

        $b = new MysqlDatabase($domain);
        $b
            ->setName('lampcpsql2')
            ->setPassword('test123')
            ->setComment('test');

        $c = new MysqlDatabase($domain);
        $c
            ->setName('lampcpsql3')
            ->setPassword($cs->encrypt('test123'))
            ->setComment('test');

        return array(
            array($a),
            array($b),
            array($c),
        );
    }

    /**
     * Test transform.
     *
     * @param MysqlDatabase $database
     *
     * @dataProvider dataProvider
     */
    public function testTransform(MysqlDatabase $database)
    {
        $model = $this->transformer->transform($database);

        $this->assertInstanceOf('\Jeboehm\Lampcp\MysqlBundle\Model\Database', $model);
        $this->assertGreaterThan(0, count($model->getUsers()->count()));

        /** @var User $user */
        $user = $model
            ->getUsers()
            ->first();

        $this->assertEquals('test123', $user->getPassword());
    }
}
