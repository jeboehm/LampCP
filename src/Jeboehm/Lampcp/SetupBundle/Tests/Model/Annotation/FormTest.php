<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Tests\Model\Annotation;

use Jeboehm\Lampcp\SetupBundle\Model\Annotation\Form;

/**
 * Class FormTest
 *
 * @package Jeboehm\Lampcp\SetupBundle\Tests\Model\Annotation
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test invalid type setting.
     *
     * @expectedException \Jeboehm\Lampcp\SetupBundle\Model\Exception\UnknownInputTypeException
     */
    public function testInvalidTypeSetting()
    {
        $test = new Form(array(
                              'type' => 'asd',
                         ));
    }

    /**
     * Test valid type setting.
     */
    public function testValidTypeSetting()
    {
        $test = new Form(array(
                              'type' => 'ask',
                         ));

        $this->assertEquals('ask', $test->getType());
    }

    /**
     * Test forbidden annotation name setting.
     *
     * @expectedException \Jeboehm\Lampcp\SetupBundle\Model\Exception\ForbiddenAnnotationNameException
     */
    public function testForbiddenAnnotationNameSetting()
    {
        $test = new Form(array(
                              'name' => 'test',
                         ));
    }

    /**
     * Test invalid annotation name setting.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAnnotationNameSetting()
    {
        $test = new Form(array(
                              'askdasd' => 'asasdsad',
                         ));
    }

    /**
     * Test name getter / setter.
     */
    public function testNameGetterSetter()
    {
        $test = new Form(array(
                              'type' => 'ask',
                         ));

        $test->setName('name');
        $this->assertEquals('name', $test->getName());
    }
}
