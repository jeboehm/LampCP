<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Collection;

use Jeboehm\Lampcp\MysqlBundle\Collection\DatabaseCollection;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;

/**
 * Class DatabaseCollectionTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Collection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DatabaseCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test findByName.
     */
    public function testFindByName()
    {
        $coll = new DatabaseCollection();
        $a    = new Database();
        $b    = new Database();
        $c    = new Database();

        $a->setName('7777');
        $b->setName('8888');
        $c->setName('9999');

        $coll->add($a);
        $coll->add($b);
        $coll->add($c);

        $this->assertEquals($b, $coll->findByName('8888'));
        $this->assertNotEquals($a, $coll->findByName('9999'));
        $this->assertNull($coll->findByName('1234'));
    }

    /**
     * Test findByNameStartsWith.
     */
    public function testFindByNameStartsWith()
    {
        $coll = new DatabaseCollection();
        $a    = new Database();
        $b    = new Database();
        $c    = new Database();

        $a->setName('7777');
        $b->setName('7788');
        $c->setName('9999');

        $coll->add($a);
        $coll->add($b);
        $coll->add($c);

        $this->assertCount(2, $coll->findByNameStartsWith('77'));
        $this->assertCount(1, $coll->findByNameStartsWith('777'));
        $this->assertCount(0, $coll->findByNameStartsWith('aa'));
    }
}
