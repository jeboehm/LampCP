<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntity;
use Jeboehm\Lampcp\CoreBundle\Entity\ConfigGroup;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;

/**
 * Class ConfigServiceTest
 *
 * @package Jeboehm\Lampcp\ConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigServiceTest extends WebTestCase {
    /** @var ConfigService */
    protected $_cs;

    /** @var ConfigEntity */
    protected $_testEntity;

    /**
     * Set up.
     */
    public function setUp() {
        $this->_cs = $this
            ->createClient()
            ->getContainer()
            ->get('config');

        $this->_cs->setEm($this->_getMockEntityManager());
    }

    /**
     * Tear down.
     */
    protected function tearDown() {
        parent::tearDown();

        $this->_testEntity = null;
    }

    /**
     * Get repository mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockRepository() {
        $repository = $this
            ->getMockBuilder('Jeboehm\Lampcp\CoreBundle\Entity\ConfigEntityRepository')
            ->setMethods(array('findOneByNameAndGroup'))
            ->disableOriginalConstructor()
            ->getMock();

        $repository
            ->expects($this->atLeastOnce())
            ->method('findOneByNameAndGroup')
            ->will($this->returnValue($this->_getConfigEntity()));

        return $repository;
    }

    /**
     * Get entity manager mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockEntityManager() {
        $em = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(array('getRepository', 'flush', 'persist'))
            ->disableOriginalConstructor()
            ->getMock();

        $em
            ->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(true));

        $em
            ->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(true));

        $em
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->will($this->returnValue($this->_getMockRepository()));

        return $em;
    }

    /**
     * Get test config entity.
     *
     * @return ConfigEntity
     */
    protected function _getConfigEntity() {
        if ($this->_testEntity) {
            return $this->_testEntity;
        }

        $group = new ConfigGroup();
        $group
            ->setName('testgroup')
            ->setId(5);

        $entity = new ConfigEntity();
        $entity
            ->setName('test')
            ->setConfiggroup($group)
            ->setId(10)
            ->setType(ConfigTypes::TYPE_STRING)
            ->setValue('testvalue');

        return $this->_testEntity = $entity;
    }

    /**
     * Test get paramater.
     */
    public function testGetParameter() {
        $this->assertEquals($this
            ->_getConfigEntity()
            ->getValue(), $this->_cs->getParameter('testgroup.test'));
    }

    /**
     * Test set parameter.
     */
    public function testSetParameter() {
        $this->_cs->setParameter('testgroup.test', 'newTestValue');
        $this->assertEquals('newTestValue', $this
            ->_getConfigEntity()
            ->getValue());
    }
}
